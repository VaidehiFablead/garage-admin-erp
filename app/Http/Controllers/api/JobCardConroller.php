<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobCardConroller extends Controller
{
    public function getAllJobCards(Request $request)
    {
        $authUser = Auth::guard('api')->user();
        $selectedSubAdminId = $request->input('selectedSubAdminId');
        $currentYear = now()->year;
        $perPage = (int) $request->input('per_page', 10);

        $baseQuery = JobCard::query()
            ->where('type', 'jobCard')
            ->where('isDeleted', 0);

        if (strtolower($authUser->role) === 'staff') {
            // $baseQuery->where('user_id', $authUser->branch_id);
            $baseQuery->where('user_id', $authUser->id);
        } else {
            $baseQuery->where('subbranch_id', $selectedSubAdminId ?: $authUser->id);
        }

        $customerQuery = clone $baseQuery;
        $query = clone $baseQuery;

        $query->with(['customer', 'paymentStore'])->latest();

        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        } elseif (!$request->has('year') || $request->query('year') === null) {
            $query->whereYear('date', $currentYear);
        }

        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->filled('customer')) {
            $query->where('customer_id', $request->customer);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', Carbon::parse($request->date));
        }

        $totalsQuery = clone $query;
        $allFilteredJobCards = $totalsQuery->get();
        $jobCards = $query->paginate($perPage);

        $customers = $customerQuery
            ->with('customer:id,name')
            ->get()
            ->pluck('customer')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                ];
            })
            ->values();

        $totalLabour = 0;
        $totalParts = 0;
        $totalGrand = 0;

        foreach ($allFilteredJobCards as $jobCard) {
            $labourDetails = json_decode($jobCard->labour_details, true) ?? [];
            $labourGst = json_decode($jobCard->labour_gst, true) ?? [];

            foreach ($labourDetails as $labour) {
                $lineTotal = (float) ($labour['price'] ?? 0);

                if (is_array($labourGst)) {
                    foreach ($labourGst as $tax) {
                        $lineTotal += $lineTotal * ((float) ($tax['rate'] ?? 0) / 100);
                    }
                }

                $totalLabour += $lineTotal;
            }

            $totalParts += (float) ($jobCard->total_parts ?? 0);
            $totalGrand += (float) ($jobCard->total_subtotal_amount ?? 0);
        }

        $data = $jobCards->getCollection()->map(function ($jobCard) {
            $labourDetails = json_decode($jobCard->labour_details, true) ?? [];
            $labourGst = json_decode($jobCard->labour_gst, true) ?? [];
            $cardLabourTotal = 0;

            foreach ($labourDetails as $labour) {
                $lineTotal = (float) ($labour['price'] ?? 0);

                if (is_array($labourGst)) {
                    foreach ($labourGst as $tax) {
                        $lineTotal += $lineTotal * ((float) ($tax['rate'] ?? 0) / 100);
                    }
                }

                $cardLabourTotal += $lineTotal;
            }

            $cardPartsTotal = (float) ($jobCard->total_parts ?? 0);
            $cardGrandTotal = (float) ($jobCard->total_subtotal_amount ?? 0);

            return [
                'id' => $jobCard->id,
                'job_card_number' => $jobCard->job_card_number,
                'type' => $jobCard->type,
                'date_formatted' => $jobCard->date ? Carbon::parse($jobCard->date)->format('d-m-Y') : '-',
                'quotation_status' => $jobCard->quotation_status, // <-- ADD THIS
                'customer_id' => optional($jobCard->customer)->id,
                'customer_name' => optional($jobCard->customer)->name,
                'vehicle_no' => $jobCard->vehicle_no,
                'total_labour' => number_format($cardLabourTotal, 2, '.', ''),
                'total_parts' => number_format($cardPartsTotal, 2, '.', ''),
                'grand_total' => number_format($cardGrandTotal, 2, '.', ''),
            ];
        })->values();
        // dd($data);

        return response()->json([
            'status' => true,
            'data' => $data,
            'customers' => $customers,
            'totals' => [
                'labour' => number_format($totalLabour, 2, '.', ''),
                'parts' => number_format($totalParts, 2, '.', ''),
                'grand' => number_format($totalGrand, 2, '.', ''),
            ],
            'pagination' => [
                'total' => $jobCards->total(),
                'per_page' => $jobCards->perPage(),
                'current_page' => $jobCards->currentPage(),
                'last_page' => $jobCards->lastPage(),
                'from' => $jobCards->firstItem(),
                'to' => $jobCards->lastItem(),
            ],
        ]);
    }
}
