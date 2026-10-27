<?php

namespace Database\Seeders;

use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\AutoCarModel;
use App\Models\User;

class JobCardSeeder extends Seeder
{
    private function parseDecimal($value)
    {
        return ($value === null || $value === '') ? 0 : (float) $value;
    }

    private function loadCsv($filename)
    {
        $path = database_path('seeders/' . $filename);
        $rows = [];
        if (!file_exists($path)) {
            return $rows;
        }
        $handle = fopen($path, "r");
        $header = fgetcsv($handle, 1000, ",");
        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
            $rows[] = array_combine($header, $row);
        }
        fclose($handle);
        return $rows;
    }

    public function run(): void
    {
        $now = Carbon::now();

        // Load all CSV files
        $jobcards     = $this->loadCsv('JobCard.csv');
        $labours      = $this->loadCsv('JobCardLabour.csv');
        $parts        = $this->loadCsv('JobCardPart.csv');
        $customers    = $this->loadCsv('CustomerMaster.csv');
        $workers      = $this->loadCsv('WorkerMaster.csv');
        $vehicles     = $this->loadCsv('Cust_Vehicel.csv');

        // --- Prepare lookup arrays ---
        $customerLookup = [];
        foreach ($customers as $c) {
            $customerLookup[$c['cCustomerCode']] = $c;
        }

        $workerLookup = [];
        foreach ($workers as $w) {
            $workerLookup[$w['cWorkerCode']] = $w;
        }

        $vehicleLookup = [];
        foreach ($vehicles as $v) {
            $vehicleLookup[$v['VehicelID']] = $v['Reg_no']; // ✅ map VehicelID -> Reg_no
        }

        foreach ($jobcards as $data) {
            $jobDate = !empty($data['Jdate'])
                ? Carbon::createFromFormat('m/d/Y', $data['Jdate'])->format('Y-m-d')
                : null;

            $nextDueDate = $jobDate ? Carbon::parse($jobDate)->addMonths(3)->format('Y-m-d') : null;

            // ✅ Find AutoCarModel ID
            $autoCarModel = AutoCarModel::where('model_name', $data['Model'])->first();
            $autoCarModelId = $autoCarModel ? $autoCarModel->id : null;

            // ✅ Get Vehicle Reg_no from Cust_Vehicel.csv
            $vehicleNo = null;
            if (!empty($data['Reg_no'])) {
                $vehicleNo = $data['Reg_no']; // direct from JobCard if available
            } elseif (!empty($data['VehicelID']) && isset($vehicleLookup[$data['VehicelID']])) {
                $vehicleNo = $vehicleLookup[$data['VehicelID']]; // fetch Reg_no from Cust_Vehicel.csv
            }

            // ✅ Insert Customer if not exists
            $customerId = null;
            if (!empty($data['CustID']) && isset($customerLookup[$data['CustID']])) {
                $cust = $customerLookup[$data['CustID']];

                // Create or get the User
                $user = User::firstOrCreate(
                    ['name' => $cust['vCustomerName'] ?? 'Unknown'],
                    [
                        'email'      => null,
                        'password'   => null,
                        'phone'      => $cust['vMobile'] ?? $cust['vPhone'] ?? null,
                        'role'       => 'customer',
                        'isDeleted'  => 0,
                        'branch_id'  => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );

                $customerId = $user->id;

                // Create or update user_detail
                UserDetail::updateOrCreate(
                    ['user_id' => $customerId],
                    [
                        'address'    => $cust['vAddress'] ?? null,
                        'city'       => $cust['vCity'] ?? null,
                        'country'    => $cust['vCountry'] ?? null,
                        'isDeleted'  => 0,
                        'branch_id'  => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }


            // ✅ Insert Worker if not exists
            $workerId = null;
            if (!empty($data['WorkerCode']) && isset($workerLookup[$data['WorkerCode']])) {
                $wrk = $workerLookup[$data['WorkerCode']];
                $user = User::firstOrCreate(
                    ['name' => $wrk['vWorkerName'] ?? 'Unknown Worker'],
                    [
                        'email'      => null,
                        'password'   => null,
                        'phone'      => null,
                        'role'       => 'Staff',
                        'isDeleted'  => $wrk['ActiveWorker'] === 'TRUE' ? 0 : 1,
                        'branch_id'  => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
                $workerId = $user->id;

                UserDetail::updateOrCreate(
                    ['user_id' => $workerId],
                    [
                        'address'    => null,
                        'city'       => null,
                        'country'    => null,
                        'isDeleted'  => 0,
                        'branch_id'  => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }

            // --- Build labour_details ---
            $labourDetails = [];
            $labourRows = array_filter($labours, fn($l) => $l['JobcardNo'] === $data['JobcardNo']);
            $counter = 1;
            foreach ($labourRows as $l) {
                $labourDetails[$counter] = [
                    'id'    => (string) $counter,
                    'price' => number_format($this->parseDecimal($l['Amount']), 2, '.', ''),
                    'total' => number_format($this->parseDecimal($l['Totalamoutaftertax']), 2, '.', ''),
                ];
                $counter++;
            }

            // --- Build parts_details ---
            $partsDetails = [];
            $partRows = array_filter($parts, fn($p) => $p['JobcardNo'] === $data['JobcardNo']);
            $counter = 1;
            $totalPartsAmount = 0;

            foreach ($partRows as $p) {
                $qty   = $this->parseDecimal($p['Qty']);
                $rate  = $this->parseDecimal($p['Rate']);
                $total = $qty * $rate;

                $partsDetails[$counter] = [
                    'id'    => (string) $counter,
                    'name'  => $p['PartCode'],
                    'price' => number_format($rate, 2, '.', ''),
                    'qty'   => (string) $qty,
                    'total' => number_format($total, 2, '.', ''),
                ];

                $totalPartsAmount += $total;
                $counter++;
            }

            $lastSequential = \DB::table('job_cards')
                ->where('bill_no', 'like', '#%')
                ->orderBy('id', 'desc') // or created_at if you prefer
                ->first();

            if ($lastSequential) {
                // Strip "#" and convert to integer
                $lastNumber = (int) ltrim($lastSequential->bill_no, '#');
                $nextNumber = $lastNumber + 1;
            } else {
                // Start fresh
                $nextNumber = 1;
            }

            // Format with 6 digits
            $validated['bill_no'] = '#' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            // ✅ Insert into job_cards
            $jobcardId = DB::table('job_cards')->insertGetId([
                'job_card_number'       => $data['JobcardNo'] ?? null,
                'date'                  => $jobDate,
                // 'next_due_date'         => $nextDueDate,
                'next_due_date' => !empty($data['NextDueDate'])
                    ? Carbon::createFromFormat('m/d/Y', $data['NextDueDate'])->format('Y-m-d')
                    : $nextDueDate,
                'customer_name'         => $data['vCustomerName'] ?? null,
                'km_hours'              => $data['Cur_KM'] ?? null,
                'due_km_hours' => ((int)($data['Cur_KM'] ?? 0)) + 2500 ?? null,
                'customer_id'           => $customerId,
                'technician'            => $workerId,
                'vehicle_no'            => $vehicleNo, // ✅ Proper Reg_no resolved
                'model'                 => $autoCarModelId,
                'mobile'                => $data['vMobile'] ?? $data['phone'] ?? null,
                'total_labour'          => $this->parseDecimal($data['totalLaborwithtax'] ?? 0),
                'discount'              => $this->parseDecimal($data['Discount'] ?? 0),
                'total_parts'           => $totalPartsAmount,
                'total_subtotal_amount' => $this->parseDecimal($data['GrandTotal'] ?? 0),
                // 'status'                => $data['Jobtype'] ?? null,
                'status'                => null,
                'comment'               => $data['JobtypeNo'] ?? null,
                'address'               => $data['Address'] ?? null,
                'type'                  => 'jobCard',
                'bill_no'               => $validated['bill_no'] ?? null,
                'user_id'               => 1,
                'subbranch_id'          => 1,
                'isDeleted'             => 0,
                'labour_details'        => !empty($labourDetails) ? json_encode($labourDetails) : null,
                'parts_details'         => !empty($partsDetails) ? json_encode($partsDetails) : null,
                'payment_method'        => 'Cash',
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);

            // ✅ Insert into payment_store
            DB::table('payment_store')->insert([
                'user_id'          => $customerId ?? 1,
                'jobcard_id'       => $jobcardId,
                'payment_amount'   => $this->parseDecimal($data['GrandTotal'] ?? 0),
                'payment_date'     => $jobDate,
                'cash_amount'      => $this->parseDecimal($data['GrandTotal'] ?? 0),
                'upi_amount'       => 0,
                'payment_method'   => 'Cash',
                'payment_type'     => 'fully',
                'isDeleted'        => 0,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }
    }
}
