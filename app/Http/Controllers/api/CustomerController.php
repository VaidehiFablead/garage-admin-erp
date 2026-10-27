<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserPermission;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    // public function getAllCustomer(Request $request)
    // {
    //     $authUser = Auth::guard('api')->user();
    //     $query = User::select('id', 'name', 'email', 'phone', 'gst_num', 'profile_image')
    //         ->where('role', 'customer') // Only customers
    //         ->where('isDeleted', 0)     // Only active (not soft-deleted)
    //         ->with('details:country,user_id,city');

    //     // 🎯 Filtering logic
    //     if ($authUser->role === 'admin' && $request->input('selectedSubAdminId')) {
    //         if ($request->filled('selectedSubAdminId')) {
    //             $selectedId = $request->input('selectedSubAdminId');

    //             $query->where('branch_id', $selectedId);
    //         }
    //         // else: admin gets all customers (no filtering)
    //     } elseif ($authUser->role === 'sub-admin') {
    //         $query->where('branch_id', $authUser->id); // sub-admin only sees their branch
    //     } else {
    //         $query->where('branch_id', $authUser->id);
    //     }

    //     $customers = $query->orderBy('id', 'desc')->get()->map(function ($customer) {
    //         return [
    //             'id' => $customer->id,
    //             'name' => $customer->name,
    //             'email' => $customer->email,
    //             'phone' => $customer->phone,
    //             'gst_num' => $customer->gst_num,
    //             'profile_image' => $customer->profile_image,
    //             'country' => optional($customer->details)->country ?? '',
    //             'city' => optional($customer->details)->city ?? '',
    //         ];
    //     });

    //     return response()->json(['status' => true, 'data' => $customers]);
    // }
    public function getAllCustomer(Request $request)
    {
        $authUser = Auth::guard('api')->user();

        $perPage = $request->input('per_page', 10);
        $search  = $request->input('search', '');

        $query = User::select('id', 'name', 'email', 'phone', 'gst_num', 'profile_image')
            ->where('role', 'customer')
            ->where('isDeleted', 0)
            ->with('details:country,user_id,city');

        // ✅ Your existing role-based filtering
        // if ($authUser->role === 'admin' && $request->filled('selectedSubAdminId')) {
        //     $query->where('branch_id', $request->selectedSubAdminId);
        // } elseif ($authUser->role === 'sub-admin') {
        //     $query->where('branch_id', $authUser->id);
        // } else {
        //     $query->where('branch_id', $authUser->id);
        // }
        if ($authUser->role === 'admin' && $request->filled('selectedSubAdminId') && $request->selectedSubAdminId !== 'null') {

            $query->where('branch_id', $request->selectedSubAdminId);

        } elseif (strtolower($authUser->role) === 'sub-admin') {

            $query->where('branch_id', $authUser->id);

        } elseif (strtolower($authUser->role) === 'staff') {

            // Staff પોતાનો branch_id વાપરે
            $query->where('branch_id', $authUser->branch_id);

        } else {

            $query->where('branch_id', $authUser->id);

        }

        // ✅ Search functionality
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('gst_num', 'LIKE', "%{$search}%")
                ->orWhereHas('details', function ($subQ) use ($search) {
                    $subQ->where('country', 'LIKE', "%{$search}%")
                        ->orWhere('city', 'LIKE', "%{$search}%");
                });
            });
        }

        // ✅ Pagination instead of get()
        $customers = $query->orderByDesc('id')->paginate($perPage);

        // ✅ Transform data
        $transformed = $customers->getCollection()->map(function ($customer) {
            return [
                'id'            => $customer->id,
                'name'          => $customer->name,
                'email'         => $customer->email,
                'phone'         => $customer->phone,
                'gst_num'       => $customer->gst_num,
                'profile_image' => $customer->profile_image,
                'country'       => optional($customer->details)->country ?? '',
                'city'          => optional($customer->details)->city ?? '',
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $transformed,
            'pagination' => [
                'total'        => $customers->total(),
                'per_page'     => $customers->perPage(),
                'current_page' => $customers->currentPage(),
                'last_page'    => $customers->lastPage(),
                'from'         => $customers->firstItem(),
                'to'           => $customers->lastItem(),
            ]
        ]);
    }



    public function getCustomerById($id)
    {
        $customer = User::find($id); // Assuming you have a User model
        if ($customer) {
            return response()->json(['status' => true, 'customer' => $customer], 200);
        } else {
            return response()->json(['status' => false, 'error' => 'Customer not found'], 404);
        }
    }
    public function createCustomer(Request $request)
    {
        $authUser = Auth::guard('api')->user();
        $userId = $authUser->id;
        $validator = Validator::make($request->all(), [
            // 'customer_name' => 'required|string|max:255',
            'customer_name' => [
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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'gst_num' => 'nullable|string|max:20|unique:users,gst_num',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
        if (!empty($request->sub_admin_id) && $request->sub_admin_id !== 'null') {
            $userId = $request->sub_admin_id;
        }
        // dd($userId);
        // Create user
        $customer = new User();
        $customer->name = $request->customer_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->state_code = $request->state_code;
        $customer->state_name = $request->state_name;
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('customer', 'public');
            $customer->profile_image = $path;
        }
        // Set the gst_num field
        $customer->gst_num = $request->gst_num;
        $customer->role = 'customer';
        $customer->branch_id = $userId;
        $customer->status = 1;
        $customer->save(); // Save user first to get its ID

        // Now create user details using the saved user's ID
        $customerDetail = new UserDetail();
        $customerDetail->user_id = $customer->id; // Use the newly created user's ID
        $customerDetail->country = $request->country;
        $customerDetail->city = $request->city;
        $customerDetail->address = $request->address;
        // $customerDetail->branch_id = $userId;
        $customerDetail->save();

        return response()->json(['status' => true, 'message' => 'Customer created successfully!']);
    }

    public function deleteCustomer(Request $request, $id)
    {
        $customer = User::find($id);

        if (!$customer) {
            return response()->json(['status' => false, 'error' => 'Customer not found'], 404);
        }

        // Check if the customer has any orders
        $hasOrders = Order::where('user_id', $id)
        ->where('isDeleted', 0) // exclude deleted
        ->exists();

        if ($hasOrders) {
            return response()->json(['status' => false, 'error' => 'This customer cannot be deleted because they have placed orders.'], 400);
        }

        DB::beginTransaction();

        try {
            // Delete related user details
            UserDetail::where('user_id', $id)->update(['isDeleted' => 1]);

            // Soft delete the customer
            $customer->isDeleted = 1;
            $customer->save();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Customer deleted successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => 'Error: ' . $e->getMessage()], 500);
        }
    }


    public function getCustomer($id)
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

    public function updateCustomer(Request $request, $id)
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
        $customer->gst_num = $request->gst_num;
        $customer->state_code = $request->state_code;
        $customer->state_name = $request->state_name;
        $customer->phone = $request->phone;

        // Handle profile image update
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('customer', 'public');
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
    // public function getCustomerProfile($id)
    // {
    //     $user = User::with('userDetail')->find($id);

    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }

    //     return response()->json([
    //         'name' => $user->name,
    //         'email' => $user->email,
    //         'phone' => $user->phone,
    //         'gst_num' => $user->gst_num,
    //         'role' => $user->role,
    //         'profile_image' => $user->profile_image,
    //         'address' => $user->userDetail->address ?? 'N/A',
    //         'city' => $user->userDetail->city ?? 'N/A',
    //         'country' => $user->userDetail->country ?? 'N/A',
    //     ]);
    // }

    public function getCustomerProfile($id)
    {
        $user = User::with('userDetail')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Get permissions with module name
        $permissions = UserPermission::with('module')
            ->where('user_id', $id)
            ->get()
            ->map(function ($perm) {
                return [
                    'module' => $perm->module->module ?? 'Unknown',
                    'view' => (bool) $perm->view,
                    'add' => (bool) $perm->add,
                    'edit' => (bool) $perm->edit,
                    'delete' => (bool) $perm->delete,
                ];
            });

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'gst_num' => $user->gst_num,
            'state_code' => $user->state_code,
            'role' => $user->role,
            'profile_image' => $user->profile_image,
            'address' => $user->userDetail->address ?? 'N/A',
            'city' => $user->userDetail->city ?? 'N/A',
            'country' => $user->userDetail->country ?? 'N/A',
            'permissions' => $permissions,
        ]);
    }
}
