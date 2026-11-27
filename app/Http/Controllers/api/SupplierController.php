<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Product;
use App\Models\Purchases;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use App\Services\ApiKeyService;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
     public function fetch(Request $request)
    {
        $gst = trim((string) $request->input('gst_number'));

        if (!$gst) {
            return response()->json(['error' => 'GST number is required'], 400);
        }

        // Get dynamic GST API tokens (primary + backup fallback)
        $tokens = ApiKeyService::getCandidates('GST');
        if (empty($tokens)) {
            $tokens = ['3.ebf2623c-44a2-49c8-8e11-aefa342f60f3_a1cfc03847ba734d20b6f9c1f80018474999aec0809348357afe263a98a99267'];
        }

        $apiUrl = "https://api.clear.in/gst/api/v0.2/taxable_entities/123456/gstin_verification?gstin={$gst}";
        $response = null;
        $lastException = null;

        $options = ['verify' => false];
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            $options['curl'] = [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4];
        }

        foreach ($tokens as $apiToken) {
            try {
                $response = Http::withoutVerifying()
                    ->connectTimeout(10)
                    ->timeout(20)
                    ->retry(2, 300)
                    ->withOptions($options)
                    ->withHeaders([
                        'X-Cleartax-Auth-Token' => $apiToken,
                        'Accept'                => 'application/json',
                    ])->get($apiUrl);

                // If successful or explicit business error (404 / 422), stop trying
                if ($response->successful() || in_array($response->status(), [400, 404, 422], true)) {
                    break;
                }
            } catch (\Throwable $e) {
                $lastException = $e;
                Log::warning('ClearTax GST API attempt failed with token: ' . $e->getMessage());
            }
        }

        if (!$response) {
            $errMsg = $lastException ? $lastException->getMessage() : 'GST verification service unavailable';
            Log::error('ClearTax GST API Error: ' . $errMsg, ['gst' => $gst]);
            return response()->json(['error' => 'GST verification service unavailable: ' . $errMsg], 500);
        }

        if ($response->failed()) {
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? 'API request failed';
            return response()->json(['error' => $errorMessage], $response->status() >= 400 && $response->status() < 500 ? $response->status() : 500);
        }

        $data = $response->json();
        $info = $data['taxpayerInfo'] ?? $data;

        if (isset($data['success']) && $data['success'] === false) {
            return response()->json(['error' => $data['message'] ?? 'Invalid GST or no data found'], 404);
        }

        if (!isset($info['gstin']) && !isset($info['tradeNam']) && !isset($info['lgnm'])) {
            return response()->json(['error' => 'Invalid GST or no data found'], 404);
        }

        // Company / Business Details
        $legalName = $info['lgnm'] ?? ($info['tradeNam'] ?? '');
        $companyName = $info['tradeNam'] ?? ($info['lgnm'] ?? '');
        $gstin = $info['gstin'] ?? $gst;
        $businessType = $info['ctb'] ?? '';
        $status = $info['sts'] ?? '';
        $registrationDate = $info['rgdt'] ?? '';
        $taxpayerType = $info['dty'] ?? '';
        $natureOfBusiness = $info['nba'] ?? [];
        $lastUpdated = $info['lastUpdated'] ?? ($info['lstupdt'] ?? '');
        $frequency = $info['filingFreq'] ?? ($info['frequencyType'] ?? '');
        $pradr = $info['pradr'] ?? [];

        // Primary Address
        $primaryAddr = $pradr['addr'] ?? [];
        $address = $pradr['fullAddress'] ?? null;
        if (empty($address)) {
            $addrParts = array_filter([
                $primaryAddr['bno'] ?? '',
                $primaryAddr['flno'] ?? '',
                $primaryAddr['bnm'] ?? '',
                $primaryAddr['st'] ?? '',
                $primaryAddr['loc'] ?? '',
                $primaryAddr['dst'] ?? '',
                $primaryAddr['stcd'] ?? '',
                $primaryAddr['pncd'] ?? '',
            ]);
            $address = implode(', ', $addrParts);
        }

        // Additional Addresses
        $additionalAddresses = [];
        if (isset($info['adadr']) && is_array($info['adadr'])) {
            foreach ($info['adadr'] as $addrItem) {
                $addr = $addrItem['addr'] ?? [];
                $adParts = array_filter([
                    $addr['bno'] ?? '',
                    $addr['flno'] ?? '',
                    $addr['bnm'] ?? '',
                    $addr['st'] ?? '',
                    $addr['loc'] ?? '',
                    $addr['dst'] ?? '',
                    $addr['stcd'] ?? '',
                    $addr['pncd'] ?? '',
                ]);
                $additionalAddresses[] = [
                    'name' => $addr['bnm'] ?? '',
                    'address' => $addrItem['fullAddress'] ?? implode(', ', $adParts),
                    'city' => $addr['city'] ?? ($addr['dst'] ?? ($addr['loc'] ?? '')),
                    'state' => $addr['stcd'] ?? '',
                    'nature' => $addrItem['ntr'] ?? '',
                ];
            }
        }

        $city = $primaryAddr['city'] ?? ($primaryAddr['dst'] ?? ($primaryAddr['loc'] ?? ''));
        $country = $primaryAddr['country'] ?? ($info['country'] ?? 'India');
        $stateName = $primaryAddr['stcd'] ?? '';

        $stateCode = '';
        $candidateCode = substr(trim($gstin), 0, 2);
        if (is_numeric($candidateCode)) {
            $stateCode = $candidateCode;
        } else {
            $stateCodeMap = [
                'JAMMU AND KASHMIR' => '01',
                'HIMACHAL PRADESH' => '02',
                'PUNJAB' => '03',
                'CHANDIGARH' => '04',
                'UTTARAKHAND' => '05',
                'HARYANA' => '06',
                'DELHI' => '07',
                'RAJASTHAN' => '08',
                'UTTAR PRADESH' => '09',
                'BIHAR' => '10',
                'SIKKIM' => '11',
                'ARUNACHAL PRADESH' => '12',
                'NAGALAND' => '13',
                'MANIPUR' => '14',
                'MIZORAM' => '15',
                'TRIPURA' => '16',
                'MEGHALAYA' => '17',
                'ASSAM' => '18',
                'WEST BENGAL' => '19',
                'JHARKHAND' => '20',
                'ODISHA' => '21',
                'CHHATTISGARH' => '22',
                'CHATTISGARH' => '22',
                'MADHYA PRADESH' => '23',
                'GUJARAT' => '24',
                'DAMAN AND DIU' => '26',
                'DADRA AND NAGAR HAVELI' => '26',
                'DADRA AND NAGAR HAVELI AND DAMAN AND DIU' => '26',
                'MAHARASHTRA' => '27',
                'ANDHRA PRADESH' => '37',
                'KARNATAKA' => '29',
                'GOA' => '30',
                'LAKSHADWEEP' => '31',
                'KERALA' => '32',
                'TAMIL NADU' => '33',
                'PUDUCHERRY' => '34',
                'ANDAMAN AND NICOBAR ISLANDS' => '35',
                'TELANGANA' => '36',
                'LADAKH' => '38',
                'OTHER TERRITORY' => '97',
            ];
            $upperState = strtoupper(trim($stateName));
            $stateCode = $stateCodeMap[$upperState] ?? '';
        }

        return response()->json([
            'gstin' => $gstin,
            'company_name' => $companyName,
            'legal_name' => $legalName,
            'business_type' => $businessType,
            'taxpayer_type' => $taxpayerType,
            'status' => $status,
            'registration_date' => $registrationDate,
            'nature_of_business' => $natureOfBusiness,
            'primary_address' => $address,
            'address' => $address,
            'city' => $city,
            'state_code' => $stateCode,
            'state' => $stateName ?: $stateCode,
            'state_name' => $stateName,
            'pin_code' => $primaryAddr['pncd'] ?? '',
            'pincode' => $primaryAddr['pncd'] ?? '',
            'pan_number' => strlen(trim($gstin)) >= 12 ? substr(trim($gstin), 2, 10) : '',
            'country' => $country,
            'last_updated' => $lastUpdated,
            'frequency' => $frequency,
            'additional_addresses' => $additionalAddresses,
        ]);
    }
    // public function getAllSupplier(Request $request)
    // {
    //     $authUser = Auth::guard('api')->user();
    //     // dd($request->has('selectedSubAdminId'));
    //     $query = User::select('id', 'name', 'email', 'phone', 'gst_num', 'profile_image')
    //         ->where('role', 'vendor')
    //         ->where('isDeleted', 0)
    //         ->with('details:country,user_id,city');

    //     // Role-based filtering
    //     if ($authUser->role === 'admin' && $request->selectedSubAdminId) {
    //         if ($request->has('selectedSubAdminId') && !empty($request->selectedSubAdminId)) {
    //             $selectedId = (int) $request->selectedSubAdminId;
    //             $query->where('branch_id', $selectedId);
    //         }
    //         // Admin without filter: show all vendors
    //     } elseif ($authUser->role === 'sub-admin') {
    //         $query->where('branch_id', $authUser->id);
    //     }else{
    //          $query->where('branch_id', $authUser->id);
    //     }

    //     $suppliers = $query->orderBy('id', 'desc')->get()->map(function ($supplier) {
    //         return [
    //             'id' => $supplier->id,
    //             'name' => $supplier->name,
    //             'email' => $supplier->email,
    //             'phone' => $supplier->phone,
    //             'gst_num' => $supplier->gst_num,
    //             'profile_image' => $supplier->profile_image,
    //             'country' => optional($supplier->details)->country ?? '',
    //             'city' => optional($supplier->details)->city ?? '',
    //         ];
    //     });

    //     return response()->json([
    //         'status' => true,
    //         'data' => $suppliers
    //     ]);
    // }

     public function getAllSupplier(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userBranchId = $user->id ?? null;

        if ($user->role === 'staff' && $user->branch_id) {
            $userBranchId = $user->branch_id;
        } elseif (!empty($request->selectedSubAdminId)) {
            $userBranchId = $request->selectedSubAdminId;
        } else {
            $userBranchId = $user->id;
        }

        $perPage = $request->get('per_page', 10); // default 10
        $search = $request->get('search', '');

        $query = User::select('id', 'name', 'email', 'phone', 'profile_image', 'gst_num')
            ->where('role', 'vendor')
            ->where('isDeleted', 0)
            ->where('branch_id', $userBranchId)
            ->with('details:country,user_id,city');

        // Apply search if provided
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('gst_num', 'LIKE', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('id', 'desc')->paginate($perPage);

        // Transform data
        $data = $suppliers->map(function ($supplier) {
            return [
                'id'                => $supplier->id,
                'name'              => $supplier->name,
                'email'             => $supplier->email,
                'phone'             => $supplier->phone,
                'profile_image'     => $supplier->profile_image,
                'profile_image_url' => $supplier->profile_image_url,
                'country'           => $supplier->details ? $supplier->details->country : '',
                'city'              => $supplier->details ? $supplier->details->city : '',
                'gst_number'        => $supplier->gst_number,
                'pan_number'        => $supplier->pan_number,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $suppliers->currentPage(),
                'last_page' => $suppliers->lastPage(),
                'per_page' => $suppliers->perPage(),
                'total' => $suppliers->total(),
                'from' => $suppliers->firstItem(),
                'to' => $suppliers->lastItem(),
            ]
        ]);
    }

    public function getSupplierById($id)
    {
        $customer = User::find($id); // Assuming you have a User model
        if ($customer) {
            return response()->json(['status' => true, 'customer' => $customer], 200);
        } else {
            return response()->json(['status' => false, 'error' => 'Customer not found'], 404);
        }
    }
    public function createSupplier(Request $request)
    {
        $authUser = Auth::guard('api')->user();
        $userId = $authUser->id;
        $validator = Validator::make($request->all(), [
            // 'name' => 'required|string|max:255',
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s.,\'-]+$/u' // letters, numbers, space, dot, comma, apostrophe, hyphen only
            ],
            'email' => 'required|email|unique:users,email', // Ensure checking in `users` table
            'phone' => 'required|numeric|digits:10|unique:users,phone',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state_code' => 'nullable|string|max:50',
            'state_name' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
            'gst_num' => 'nullable|string|max:20|unique:users,gst_num',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
        if (!empty($request->sub_admin_id) && $request->sub_admin_id !== 'null') {
            $userId = $request->sub_admin_id;
        }
        // Create user
        $customer = new User();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->state_code = $request->state_code;
        $customer->state_name = $request->state_name;
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('vendor', 'public');
            $customer->profile_image = $path;
        }
        $customer->gst_num = $request->gst_num;
        $customer->role = 'vendor';
        $customer->branch_id = $userId;
        $customer->status = 1;
        $customer->save(); // Save user first to get its ID

        // Now create user details using the saved user's ID
        $customerDetail = new UserDetail();
        $customerDetail->user_id = $customer->id; // Use the newly created user's ID
        $customerDetail->country = $request->country;
        $customerDetail->city = $request->city;
        $customerDetail->address = $request->address;
        $customerDetail->save();

        return response()->json([
            'status' => true,
            'message' => 'Customer created successfully!',
            'vendor' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone
            ]
        ]);
    }

    public function deleteSupplier(Request $request, $id)
    {
        $supplier = User::find($id);

        if (!$supplier || $supplier->role !== 'vendor') {
            return response()->json(['status' => false, 'error' => 'Vendor not found'], 404);
        }

        // Check if vendor is associated with any purchase invoices
        $hasPurchases = Purchases::where('vendor_id', $id)->where('isDeleted', 0)->exists();

        if ($hasPurchases) {
            return response()->json([
                'status' => false,
                'error' => 'This vendor cannot be deleted because they are associated with purchase.'
            ], 400);
        }

        try {
            // Delete related user details (soft delete or hard delete as per your logic)
            UserDetail::where('user_id', $id)->update(['isDeleted' => 1]); // Optional if UserDetail has isDeleted

            // Soft delete the supplier by setting isDeleted = 1
            $supplier->isDeleted = 1;
            $supplier->save();

            return response()->json([
                'status' => true,
                'message' => 'Vendor deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getSupplier($id)
    {
        // Find customer and include details
        $customer = User::with('details')->find($id);

        if (!$customer) {
            return response()->json(['status' => false, 'error' => 'Customer not found'], 404);
        }

        return response()->json([
            'status' => true,
            'customer' => $customer,
        ]);
    }

    public function updateSupplier(Request $request, $id)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            // 'customer_name' => 'required|string|max:255',
            'customer_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s.,\'-]+$/u' // letters, numbers, space, dot, comma, apostrophe, hyphen only
            ],
            'email' => "required|email|unique:users,email,$id", // Unique but ignores current user
            'phone' => "required|numeric|digits:10|unique:users,phone,$id", // Unique & exactly 10 digits
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state_code' => 'nullable|string|max:50',
            'state_name' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048', // Optional profile image
            'gst_num' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'gst_num')->ignore($id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        // Find the user
        $customer = User::find($id);
        if (!$customer) {
            return response()->json(['status' => false, 'error' => 'Customer not found'], 404);
        }

        // Update user details
        $customer->name = $request->customer_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->gst_num = $request->gst_num;
        $customer->state_code = $request->state_code;
        $customer->state_name = $request->state_name;

        // Handle profile image update
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('vendor', 'public');
            $customer->profile_image = $path;
        }

        $customer->save();

        // Update related user details
        $customerDetail = UserDetail::where('user_id', $id)->first();
        if ($customerDetail) {
            $customerDetail->country = $request->country;
            $customerDetail->city = $request->city;
            $customerDetail->address = $request->address;
            $customerDetail->save();
        }

        return response()->json(['status' => true, 'message' => 'Customer updated successfully!']);
    }

    public function getVendorProfile($id)
    {
        $user = User::with('userDetail')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'gst_num' => $user->gst_num,
            'state_code' => $user->state_code,
            'role' => $user->role,
            'profile_image' => $user->profile_image,
            'address' => $user->userDetail->address ?? '',
            'city' => $user->userDetail->city ?? '',
            'country' => $user->userDetail->country ?? '',
        ]);
    }
}
