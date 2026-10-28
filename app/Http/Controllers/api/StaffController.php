<?php

namespace App\Http\Controllers\api;

use App\Models\UserPermission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Product;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Plan;

class StaffController extends Controller
{
    // public function getAllStaff(Request $request)
    // {
    //     $authUser = Auth::guard('api')->user();

    //     $query = User::select('id', 'name', 'email', 'phone', 'profile_image', 'role')
    //         ->where('role', 'Staff')
    //         ->where('isDeleted', 0)
    //         ->with('details:country,user_id,city');

    //     // 🧠 Role-based filtering
    //     if ($authUser->role === 'admin' && $request->selectedSubAdminId) {
    //         if ($request->filled('selectedSubAdminId')) {
    //             $query->where('branch_id', (int) $request->selectedSubAdminId);
    //         }
    //         // Else: show all staff
    //     } elseif ($authUser->role === 'sub-admin') {
    //         // Sub-admin only sees their own branch staff
    //         $query->where('branch_id', $authUser->id);
    //     } else {
    //         $query->where('branch_id', $authUser->id);
    //     }

    //     $staff = $query->orderBy('id', 'desc')->get()->map(function ($user) {
    //         return [
    //             'id' => $user->id,
    //             'name' => $user->name,
    //             'role' => $user->role,
    //             'email' => $user->email,
    //             'phone' => $user->phone,
    //             'profile_image' => $user->profile_image,
    //             'country' => $user->details->country ?? '',
    //             'city' => $user->details->city ?? '',
    //         ];
    //     });

    //     return response()->json(['status' => true, 'data' => $staff]);
    // }

    public function getAllStaff(Request $request)
    {
        $authUser = Auth::guard('api')->user();

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search');

        $query = User::select(
            'id',
            'name',
            'email',
            'phone',
            'profile_image',
            'role',
            'gst_num', // ✅ correct column
            'branch_id'
        )
            ->whereIn('role', ['Staff', 'staff', 'HR', 'hr'])
            ->where('isDeleted', 0)
            ->with('details:user_id,country,city,pan_number');

        // ✅ Role-based filtering
        // if ($authUser->role === 'admin') {
        //     if ($request->filled('selectedSubAdminId')) {
        //         $query->where('branch_id', (int) $request->selectedSubAdminId);
        //     }
        // } elseif ($authUser->role === 'sub-admin') {
        //     $query->where('branch_id', $authUser->id);
        // } else {
        //     $query->where('branch_id', $authUser->id);
        // }
        if ($authUser->role == 'admin') {

            $selectedBranch = $request->selectedSubAdminId;

            if (!empty($selectedBranch)) {
                // Sub Admin selected
                $query->where('branch_id', $selectedBranch);
            } else {
                // Main Branch
                $query->where('branch_id', 1); // Admin branch
            }

        } elseif ($authUser->role == 'sub-admin') {

            $query->where('branch_id', $authUser->id);

        } else {

            $query->where('branch_id', $authUser->branch_id);

        }

        // ✅ Search filter (FIXED)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('gst_num', 'LIKE', "%{$search}%") // ✅ FIXED
                ->orWhereHas('details', function ($d) use ($search) {
                    $d->where('country', 'LIKE', "%{$search}%")
                        ->orWhere('city', 'LIKE', "%{$search}%");
                });
            });
        }

        $staff = $query->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);

        $data = $staff->getCollection()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'email' => $user->email,
                'phone' => $user->phone,
                'gst_number' => $user->gst_num,
                'pan_number' => $user->details->pan_number ?? '',
                'profile_image' => $user->profile_image,
                'country' => $user->details->country ?? '',
                'city' => $user->details->city ?? '',
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data->values(),
            'active_staff_count' => User::whereIn('role', ['staff', 'Staff'])->where('isDeleted', 0)->count(),
            'user_limit' => tenant_user_limit(),
            'pagination' => [
                'current_page' => $staff->currentPage(),
                'last_page' => $staff->lastPage(),
                'per_page' => $staff->perPage(),
                'total' => $staff->total(),
                'next_page_url' => $staff->nextPageUrl(),
                'prev_page_url' => $staff->previousPageUrl(),
            ]
        ], 200);
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
    public function createStaff(Request $request)
    {
        // dd($request->all());
        $user = Auth::guard('api')->user();
        $userBranchId = $user->id ?? null;

        $validator = Validator::make($request->all(), [
            'staff_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s.,\'-]+$/u'
            ],
            'email'            => 'nullable|email|unique:users,email',
            'phone'            => 'required|numeric',
            'country'          => 'nullable|string|max:100',
            'password'         => 'required|string|min:8|max:255',
            // 'password'         => [
            //     'required', 'string', 'min:8', 'max:255',
            //     'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/'
            // ],
            'role'             => 'nullable|string',
            'city'             => 'nullable|string|max:100',
            'address'          => 'nullable|string|max:500',
            'avatar'           => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'face_photo'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'face_descriptor'  => 'nullable|string',
            'captured_photo'   => 'nullable|string',
            'department_id'    => 'nullable|integer',
            'designation_id'   => 'nullable|integer',
            'salary'           => 'nullable|numeric|min:0',
            'joining_date'     => 'nullable|date',
            'shift_time'       => 'nullable|string|max:100',
            'working_location' => 'nullable|string|max:100',
            'gst_num'          => 'nullable|string|max:20',
            'pan_number'       => 'nullable|string|max:20',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character (@$!%*?&).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. No logged-in user found.',
            ], 401);
        }


        $userBranchId = $user->id;
        if (!empty($request->sub_admin_id) && $request->sub_admin_id !== 'null') {
            $userBranchId = $request->sub_admin_id;
        }
        // dd($userBranchId);
        // ✅ Enforce dynamic user/staff limit based on tenant plan with isDeleted = 0
        $userLimit = tenant_user_limit();
        if ($userLimit !== null && $userLimit > 0) {
            $staffCount = User::whereIn('role', ['staff', 'Staff'])
                            ->where('isDeleted', 0)
                            ->count();

            if ($staffCount >= $userLimit) {
                return response()->json([
                    'status' => false,
                    'error' => "Staff limit exceeded. Your plan allows only {$userLimit} staff members.",
                    'message' => "Staff limit exceeded. Your plan allows only {$userLimit} staff members."
                ], 422);
            }
        }
        // ✅ Create user

        $customer = new User();
        $customer->name         = $request->staff_name;
        $customer->email        = $request->email;
        $customer->phone        = $request->phone;
        $customer->haspermission = $request->permission_type;
        $customer->password     = Hash::make($request->password);
        $customer->role         = $request->filled('role') ? strtolower($request->role) : 'staff';
        $customer->status       = 1;
        $customer->branch_id    = $userBranchId;
        $customer->gst_num      = $request->gst_num;

        if ($request->hasFile('avatar')) {
            $customer->profile_image = $request->file('avatar')->store('staff', 'public');
        }

        // Save face descriptor to users table
        if ($request->filled('face_descriptor')) {
            $descriptor = json_decode($request->face_descriptor, true);
            if (is_array($descriptor) && count($descriptor) === 128) {
                $customer->face_descriptor = $descriptor;
            }
        }
        $customer->save();

        // ✅ Create user details (including new job fields)
        $facePhotoPath = null;
        if ($request->hasFile('face_photo')) {
            $facePhotoPath = $request->file('face_photo')->store('staff/face', 'public');
        } elseif ($request->filled('captured_photo')) {
            // Base64 captured from camera
            $base64 = $request->input('captured_photo');
            if (str_contains($base64, ',')) {
                $base64 = explode(',', $base64)[1];
            }
            $imageName = 'face_' . $customer->id . '_' . time() . '.jpg';
            $path = 'staff/face/' . $imageName;
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, base64_decode($base64));
            $facePhotoPath = $path;
        }

        $customerDetail = new UserDetail();
        $customerDetail->user_id          = $customer->id;
        $customerDetail->country          = $request->country;
        $customerDetail->city             = $request->city;
        $customerDetail->address          = $request->address;
        $customerDetail->salary           = $request->salary ?? 0;
        $customerDetail->designation_id   = $request->designation_id;
        $customerDetail->department_id    = $request->department_id;
        $customerDetail->joining_date     = $request->joining_date;
        $customerDetail->shift_time       = $request->shift_time;
        $customerDetail->working_location = $request->working_location;
        $customerDetail->face_photo       = $facePhotoPath;
        $customerDetail->pan_number       = $request->pan_number;
        $customerDetail->save();

        // ✅ Store dynamic permissions
        if ($request->has('modules') && is_array($request->modules)) {
            foreach ($request->modules as $moduleData) {
                UserPermission::create([
                    'user_id' => $customer->id,
                    'module_id' => $moduleData['module_id'],
                    'view' => isset($moduleData['view']),
                    'add' => isset($moduleData['insert']),
                    'edit' => isset($moduleData['edit']),
                    'delete' => isset($moduleData['delete']),
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Staff created successfully with permissions!'
        ]);
    }

    public function deleteStaff(Request $request, $id)
    {
        $customer = User::find($id);

        if (!$customer) {
            return response()->json(['status' => false, 'error' => 'Customer not found'], 404);
        }

        // Check if the customer has any orders
        $hasOrders = Order::where('user_id', $id)->exists();

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


    // public function getStaff($id)
    // {
    //     // Find customer and include details
    //     $customer = User::with('details')->find($id);

    //     if (!$customer) {
    //         return response()->json(['status' => false, 'error' => 'Customer not found'], 404);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'customer' => $customer,
    //     ]);
    // }

    public function getStaff($id)
    {
        $customer = User::with('details')->find($id);
        if (!$customer) {
            return response()->json(['status' => false, 'error' => 'Customer not found'], 404);
        }

        // Get all modules
        $modules = Module::all();

        // Fetch permissions for this user
        $permissions = UserPermission::where('user_id', $id)->get();

        return response()->json([
            'status' => true,
            'customer' => $customer,
            'modules' => $modules,
            'permissions' => $permissions
        ]);
    }


    // public function updateStaff(Request $request, $id)
    // {
    //     // Validate input data
    //     $validator = Validator::make($request->all(), [
    //         'customer_name' => 'required|string|max:255',
    //         'email' => "required|email|unique:users,email,$id",
    //         'phone' => "required|numeric|digits_between:10,15|unique:users,phone,$id",
    //         'country' => 'nullable|string|max:100',
    //         'city' => 'nullable|string|max:100',
    //         'address' => 'nullable|string|max:500',
    //         'role' => 'required|string',
    //         'password' => 'nullable|string|min:6', // ✅ Allow optional password update
    //         'avatar' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     // Find the user
    //     $customer = User::find($id);
    //     if (!$customer) {
    //         return response()->json([
    //             'status' => false,
    //             'error' => 'Staff not found'
    //         ], 404);
    //     }

    //     // Update main user fields
    //     $customer->name = $request->customer_name;
    //     $customer->email = $request->email;
    //     $customer->phone = $request->phone;

    //     $customer->role = $request->role;

    //     // ✅ Hash and update password if provided
    //     if ($request->filled('password')) {
    //         $customer->password = Hash::make($request->password);
    //     }

    //     // ✅ Store avatar in `staff` folder
    //     if ($request->hasFile('avatar')) {
    //         $path = $request->file('avatar')->store('staff', 'public');
    //         $customer->profile_image = $path;
    //     }

    //     $customer->save();

    //     // Update related user details
    //     $customerDetail = UserDetail::where('user_id', $id)->first();
    //     if ($customerDetail) {
    //         $customerDetail->country = $request->country;
    //         $customerDetail->city = $request->city;
    //         $customerDetail->address = $request->address;
    //         $customerDetail->save();
    //     }

    //     UserPermission::where('user_id', $id)->delete();

    //     foreach ($request->input('modules', []) as $moduleId => $moduleData) {
    //         UserPermission::create([
    //         'user_id' => $customer->id,
    //         'module_id' => $moduleData['module_id'],
    //         'view' => isset($moduleData['view']),
    //         'add' => isset($moduleData['insert']),
    //         'edit' => isset($moduleData['edit']),
    //         'delete' => isset($moduleData['delete']),
    //         ]);
    //     }


    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Staff updated successfully!'
    //     ]);
    // }

    // public function updateStaff(Request $request, $id)
    // {
    //     // dd($request->all());
    //     // Validate input data
    //     $validator = Validator::make($request->all(), [
    //         'staff_name' => [
    //             'required',
    //             'string',
    //             'max:255',
    //             'regex:/^[a-zA-Z0-9\s.,\'-]+$/u' // letters, numbers, space, dot, comma, apostrophe, hyphen only
    //         ],
    //         'email' => "nullable|email|unique:users,email,$id",
    //         'phone' => "required|numeric|digits_between:10,15|unique:users,phone,$id",
    //         'country' => 'nullable|string|max:100',
    //         'city' => 'nullable|string|max:100',
    //         'address' => 'nullable|string|max:500',

    //         'password' => 'nullable|string|min:3',
    //         'avatar' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     // Find the user
    //     $customer = User::find($id);
    //     if (!$customer) {
    //         return response()->json([
    //             'status' => false,
    //             'error' => 'Staff not found'
    //         ], 404);
    //     }

    //     // Update main user fields
    //     $customer->name = $request->staff_name;
    //     $customer->email = $request->email;
    //     $customer->phone = $request->phone;
    //     $customer->haspermission = $request->permission_type;
    //     $customer->role = 'Staff';

    //     if ($request->filled('password')) {
    //         $customer->password = Hash::make($request->password);
    //     }

    //     if ($request->hasFile('avatar')) {
    //         $path = $request->file('avatar')->store('staff', 'public');
    //         $customer->profile_image = $path;
    //     }

    //     $customer->save();

    //     // Update or create user details
    //     $customerDetail = UserDetail::where('user_id', $id)->first();
    //     if ($customerDetail) {
    //         $customerDetail->country = $request->country;
    //         $customerDetail->city = $request->city;
    //         $customerDetail->address = $request->address;
    //         $customerDetail->save();
    //     }

    //     // Update or create user permissions
    //     $moduleInputs = $request->input('modules', []);
    //     foreach ($moduleInputs as $moduleData) {
    //         UserPermission::updateOrCreate(
    //             [
    //                 'user_id' => $customer->id,
    //                 'module_id' => $moduleData['module_id'],
    //             ],
    //             [
    //                 'view'   => isset($moduleData['view']) ? true : false,
    //                 'add'    => isset($moduleData['add']) ? true : false,
    //                 'edit'   => isset($moduleData['edit']) ? true : false,
    //                 'delete' => isset($moduleData['delete']) ? true : false,
    //             ]
    //         );
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Staff updated successfully!'
    //     ]);
    // }
    public function updateStaff(Request $request, $id)
    {

            // Validate input data
        $validator = Validator::make($request->all(), [
            'staff_name'       => 'required|string|max:255',
            'email'            => "required|email|unique:users,email,$id",
            'phone'            => "required|numeric|digits_between:10,15|unique:users,phone,$id",
            'country'          => 'nullable|string|max:100',
            'city'             => 'nullable|string|max:100',
            'address'          => 'nullable|string|max:500',
            'role'             => 'nullable|string',
            'password'         => 'nullable|string|min:8|max:255',
            // 'password'         => [
            //     'nullable', 'string', 'min:8', 'max:255',
            //     'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/'
            // ],
            'avatar'           => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
            'face_photo'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'face_descriptor'  => 'nullable|string',
            'captured_photo'   => 'nullable|string',
            'department_id'    => 'nullable|integer',
            'designation_id'   => 'nullable|integer',
            'salary'           => 'nullable|numeric|min:0',
            'joining_date'     => 'nullable|date',
            'shift_time'       => 'nullable|string|max:100',
            'working_location' => 'nullable|string|max:100',
            'gst_num'          => 'nullable|string|max:20',
            'pan_number'       => 'nullable|string|max:20',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character (@$!%*?&).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Find the user
        $customer = User::find($id);
        if (!$customer) {
            return response()->json([
                'status' => false,
                'error' => 'Staff not found'
            ], 404);
        }

        // Update main user fields
        $customer->name          = $request->staff_name;
        $customer->email         = $request->email;
        $customer->phone         = $request->phone;
        $customer->haspermission = $request->permission_type;
        $customer->role          = $request->filled('role') ? strtolower($request->role) : $customer->role;
        $customer->gst_num       = $request->gst_num;

        if ($request->filled('password')) {
            $customer->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $customer->profile_image = $request->file('avatar')->store('staff', 'public');
        }

        // Update face descriptor on users table
        if ($request->filled('face_descriptor')) {
            $descriptor = json_decode($request->face_descriptor, true);
            if (is_array($descriptor) && count($descriptor) === 128) {
                $customer->face_descriptor = $descriptor;
            }
        }
        $customer->save();

        // Update or create user details (including new job fields)
        $facePhotoPath = null;
        if ($request->hasFile('face_photo')) {
            $facePhotoPath = $request->file('face_photo')->store('staff/face', 'public');
        } elseif ($request->filled('captured_photo')) {
            // Base64 captured from camera
            $base64 = $request->input('captured_photo');
            if (str_contains($base64, ',')) {
                $base64 = explode(',', $base64)[1];
            }
            $imageName = 'face_' . $customer->id . '_' . time() . '.jpg';
            $path = 'staff/face/' . $imageName;
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, base64_decode($base64));
            $facePhotoPath = $path;
        }

        $customerDetail = UserDetail::firstOrNew(['user_id' => $id]);
        $customerDetail->country          = $request->country;
        $customerDetail->city             = $request->city;
        $customerDetail->address          = $request->address;
        $customerDetail->salary           = $request->salary ?? $customerDetail->salary;
        $customerDetail->designation_id   = $request->designation_id ?? $customerDetail->designation_id;
        $customerDetail->department_id    = $request->department_id ?? $customerDetail->department_id;
        $customerDetail->joining_date     = $request->joining_date ?? $customerDetail->joining_date;
        $customerDetail->shift_time       = $request->shift_time ?? $customerDetail->shift_time;
        $customerDetail->working_location = $request->working_location ?? $customerDetail->working_location;
        $customerDetail->pan_number       = $request->pan_number ?? $customerDetail->pan_number;
        if ($facePhotoPath) {
            $customerDetail->face_photo = $facePhotoPath;
        }
        $customerDetail->save();

        // Update permissions according to selected permission type
        if ((int) $request->input('permission_type', 0) === 0) {
            // Without permission: remove all permissions
            UserPermission::where('user_id', $customer->id)->delete();
        } else {
        $moduleInputs = $request->input('modules', []);

            // Delete permissions for modules that are no longer submitted
            $submittedModuleIds = collect($moduleInputs)
                ->pluck('module_id')
                ->filter()
                ->map(fn($id) => (int) $id)
                ->all();

            if (!empty($submittedModuleIds)) {
                UserPermission::where('user_id', $customer->id)
                    ->whereNotIn('module_id', $submittedModuleIds)
                    ->delete();
            } else {
                // If nothing submitted but withPermission selected, clear all
                UserPermission::where('user_id', $customer->id)->delete();
            }

            // Upsert submitted permissions
        foreach ($moduleInputs as $moduleData) {
            UserPermission::updateOrCreate(
                [
                    'user_id' => $customer->id,
                    'module_id' => $moduleData['module_id'],
                ],
                [
                    'view'   => isset($moduleData['view']) ? true : false,
                    'add'    => isset($moduleData['add']) ? true : false,
                    'edit'   => isset($moduleData['edit']) ? true : false,
                    'delete' => isset($moduleData['delete']) ? true : false,
                ]
            );
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Staff updated successfully!'
        ]);
    }

    public function getCustomerProfile($id)
    {
        $user = User::with('userDetail')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'profile_image' => $user->profile_image,
            'address' => $user->userDetail->address ?? 'N/A',
            'city' => $user->userDetail->city ?? 'N/A',
            'country' => $user->userDetail->country ?? 'N/A',
        ]);
    }
}
