<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Hash;
use App\Models\UserPermission;
use Illuminate\Validation\Rule; 

class SubBranchController extends Controller
{
    public function createSubbranch(Request $request)
    {
        // ✅ Limit: max 2 active sub-admins allowed
        $subAdminCount = User::where('role', 'sub-admin')->where('isDeleted', 0)->count();
        if ($subAdminCount >= 2) {
            return response()->json([
                'status' => false,
                'error' => 'Maximum branch limit reached. You cannot create more than 2 sub-branches.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            // 'phone' => 'required|numeric|digits:10|unique:users,phone',
            'phone' => [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('users', 'phone')->where(function ($query) {
                    return $query->where('isDeleted', 0);
                }),
            ],
            'country' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8',
            // 'password' => [
            //     'nullable',
            //     'string',
            //     'min:8',
            //     'max:255',
            //     'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/'
            // ],
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ],[
            // ✅ CUSTOM ERROR MESSAGE HERE
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character (@$!%*?&).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ Create user
        $customer = new User();
        $customer->name = $request->customer_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->password = Hash::make($request->password);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('staff', 'public');
            $customer->profile_image = $path;
        }

        $customer->role = 'sub-admin';
        $customer->status = 1;
        $customer->save();

        // ✅ Create user details
        $customerDetail = new UserDetail();
        $customerDetail->user_id = $customer->id;
        $customerDetail->country = $request->country;
        $customerDetail->city = $request->city;
        $customerDetail->address = $request->address;
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
            'message' => 'Sub Branch created successfully'
        ]);
    }

    public function updateSubbranch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable    |email|unique:users,email,' . $request->id,
            // 'phone' => 'required|numeric|unique:users,phone,' . $request->id,
            'phone' => [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('users', 'phone')
                    ->ignore($request->id)
                    ->where(function ($query) {
                        return $query->where('isDeleted', 0);
                    }),
            ],
            'password' => 'nullable|string|min:8',
            // 'password' => [
            //     'nullable',
            //     'string',
            //     'min:8',
            //     'max:255',
            //     'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/'
            // ],
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ],[
            // ✅ CUSTOM ERROR MESSAGE HERE
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character (@$!%*?&).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ Fetch existing user
        $customer = User::find($request->id);
        $customer->name = $request->customer_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;

        // ✅ Only update password if provided
        if (!empty($request->password)) {
            $customer->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('staff', 'public');
            $customer->profile_image = $path;
        }

        // ✅ Do not change role if user is admin
        if ($customer->role !== 'admin') {
            $customer->role = 'sub-admin';
        }

        $customer->status = 1;
        $customer->save();

        // ✅ Update or create user details
        UserDetail::updateOrCreate(
            ['user_id' => $customer->id],
            [
                'country' => $request->country,
                'city' => $request->city,
                'address' => $request->address
            ]
        );

        // ✅ Remove old permissions if needed (optional)
        UserPermission::where('user_id', $customer->id)->delete();

        // ✅ Reinsert new permissions
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

        return response()->json(['status' => true, 'message' => 'Sub Branch updated successfully'], 200);
    }

    public function deleteSubbranch($id)
    {
        $subbranch = User::find($id);

        if (!$subbranch) {
            return response()->json([
                'status' => false,
                'error' => 'Branch not found.'
            ], 404);
        }

        // Prevent deletion of admin accounts
        if ($subbranch->role === 'admin') {
            return response()->json([
                'status' => false,
                'error' => 'Admin account cannot be deleted.'
            ], 403);
        }

        DB::transaction(function () use ($id) {
            // Helper: check column existence before deciding which field to filter on.
            // Priority: branch_id → created_by → skip

            $tables = [
                // [ table, branch_id_col, created_by_col ]
                ['orders',             'branch_id', 'created_by'],
                ['order_items',        'branch_id', 'created_by'],
                ['purchases',          'branch_id', 'created_by'],
                ['purchase_invoice',   'branch_id', null],
                ['expenses',           'branch_id', 'created_by'],
                ['products',           'branch_id', null],
                ['categories',         'branch_id', null],
                ['brands',             'branch_id', null],
                ['labour_services',    'branch_id', null],
                ['auto_car_categories','branch_id', null],
                ['auto_car_models',    'branch_id', null],
                ['expense_types',      'branch_id', null],
                ['custom_invoice',     'branch_id', null],
                ['appointments',       'branch_id', null],
                ['attendance',         'branch_id', null],
                ['salaries',           'branch_id', null],
                ['settings',           'branch_id', null],
                ['advance_payments',   'branch_id', null],
            ];

            foreach ($tables as [$table, $branchCol, $createdByCol]) {
                if (Schema::hasColumn($table, $branchCol) && Schema::hasColumn($table, 'isDeleted')) {
                    DB::table($table)->where($branchCol, $id)->update(['isDeleted' => 1]);
                } elseif ($createdByCol && Schema::hasColumn($table, $createdByCol) && Schema::hasColumn($table, 'isDeleted')) {
                    DB::table($table)->where($createdByCol, $id)->update(['isDeleted' => 1]);
                }
                // else: column doesn't exist or no isDeleted column — skip this table
            }

            // job_cards uses subbranch_id (not branch_id)
            if (Schema::hasColumn('job_cards', 'isDeleted')) {
                if (Schema::hasColumn('job_cards', 'subbranch_id')) {
                    DB::table('job_cards')->where('subbranch_id', $id)->update(['isDeleted' => 1]);
                } elseif (Schema::hasColumn('job_cards', 'user_id')) {
                    DB::table('job_cards')->where('user_id', $id)->update(['isDeleted' => 1]);
                }
            }

            // purchases_autocare uses sub_admin_id
            if (Schema::hasColumn('purchases_autocare', 'sub_admin_id') && Schema::hasColumn('purchases_autocare', 'isDeleted')) {
                DB::table('purchases_autocare')->where('sub_admin_id', $id)->update(['isDeleted' => 1]);
            }

            // payment_store and expense_store have no branch_id / created_by — skip them

            // 2. Soft-delete all staff members belonging to this branch
            $staffIds = DB::table('users')
                ->where('branch_id', $id)
                ->where('role', 'staff')
                ->pluck('id');

            if ($staffIds->isNotEmpty()) {
                DB::table('users')->whereIn('id', $staffIds)->update(['isDeleted' => 1]);
                DB::table('user_details')->whereIn('user_id', $staffIds)->update(['isDeleted' => 1]);
            }

            // 3. Soft-delete the sub-admin's own user_details
            DB::table('user_details')->where('user_id', $id)->update(['isDeleted' => 1]);

            // 4. Soft-delete the sub-admin user record itself
            DB::table('users')->where('id', $id)->update(['isDeleted' => 1]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Sub Branch and all related data deleted successfully.'
        ], 200);
    }


    public function getSubbranch($id)
    {
        $subbranch = User::with('userDetail')
            ->where('isDeleted', 0)
            ->where('id', $id)
            ->first(); // ✅ changed from get() to first()

        return response()->json(['status' => true, 'data' => $subbranch], 200);
    }



    public function getAllSubbranch()
    {
        $subbranch = User::with('userDetail')->where('isDeleted', 0)->whereIn('role', ['sub-admin', 'admin'])->orderBy('created_at', 'desc')->get();
        // dd($subbranch);
        return response()->json(['status' => true, 'data' => $subbranch], 200);
    }
}
