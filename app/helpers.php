<?php

if (! function_exists('tenant')) {
    /**
     * Current client (tenant) details from admin-products, or null when tenancy is off.
     *
     * @return mixed
     */
    function tenant(?string $key = null, $default = null)
    {
        $tenant = app(\App\Services\Tenancy\TenantManager::class)->current();

        return $key === null ? $tenant : data_get($tenant, $key, $default);
    }
}

if (! function_exists('tenant_has_module')) {
    /**
     * Check if current tenant has access to given module name(s).
     * If tenancy is disabled or not active, returns true.
     *
     * @param string|array $moduleNames
     * @return bool
     */
    function tenant_has_module($moduleNames): bool
    {
        if (! config('tenant.enabled') || ! tenant()) {
            return true;
        }

        $assigned = (array) tenant('modules', []);
        if (empty($assigned)) {
            try {
                $dbPlanFeatures = null;
                if (auth()->check() && auth()->user()->plan && !empty(auth()->user()->plan->features)) {
                    $dbPlanFeatures = auth()->user()->plan->features;
                } else {
                    $activePlan = \Illuminate\Support\Facades\DB::table('plans')->where('is_active', 1)->latest('id')->first();
                    if ($activePlan && !empty($activePlan->features)) {
                        $dbPlanFeatures = $activePlan->features;
                    }
                }
                if (!empty($dbPlanFeatures)) {
                    $assigned = is_array($dbPlanFeatures) ? $dbPlanFeatures : (json_decode($dbPlanFeatures, true) ?: []);
                }
            } catch (\Throwable) {
                // keep default assigned modules
            }
        }

        if (empty($assigned)) {
            return false;
        }

        $normalize = function ($str) {
            return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$str));
        };

        $normalizedAssigned = array_map($normalize, $assigned);

        static $aliasGroups = [
            ['erp'],
            ['allmasters', 'masters', 'master'],
            ['jobcard', 'jobcards', 'job card', 'job cards'],
            ['dailyregister', 'dailydata', 'daily register'],
            ['purchase', 'purchases'],
            ['vendor', 'vendors', 'supplier', 'suppliers'],
            ['inventory', 'manageinventory'],
            ['appointment', 'appointments', 'crm'],
            ['customer', 'customers'],
            ['accounting', 'accountledger', 'ledger'],
            ['creditdebitnotes', 'creditnotes', 'debitnotes', 'creditdebitnotestype'],
            ['gstreports', 'gstreport', 'gst'],
            ['hr', 'humanresources'],
            ['staff', 'staffs', 'employee', 'employees'],
            ['attendance', 'manageattendance'],
            ['payroll', 'salary', 'groupsalary'],
            ['advancepay', 'advancepayment'],
            ['leave', 'leaves', 'leaverequest'],
            ['myplandetails', 'plan', 'plans'],
            ['shopsettings', 'shopsetting', 'settings', 'setting', 'generalsettings'],
            ['taxrates', 'tax', 'taxrate'],
            ['dashboard'],
            ['sales', 'salesorders', 'salesandorders', 'salesinvoice'],
            ['quotations', 'quotation'],
            ['departments', 'department'],
            ['designations', 'designation'],
        ];

        $checkList = is_array($moduleNames) ? $moduleNames : [$moduleNames];
        $expandedCheckList = [];

        foreach ($checkList as $name) {
            $norm = $normalize($name);
            $expandedCheckList[$norm] = true;
            foreach ($aliasGroups as $group) {
                if (in_array($norm, $group, true)) {
                    foreach ($group as $alias) {
                        $expandedCheckList[$alias] = true;
                    }
                }
            }
        }

        foreach (array_keys($expandedCheckList) as $name) {
            if (in_array($name, $normalizedAssigned, true)) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('tenant_has_sub_module')) {
    /**
     * Check if current tenant has access to given sub-module name(s) / slug(s).
     * If $parentModuleName is given, checks if tenant has the parent module first.
     * If tenancy is disabled or not active, returns true.
     *
     * @param string|array $subModuleNames
     * @param string|array|null $parentModuleName
     * @return bool
     */
    function tenant_has_sub_module($subModuleNames, $parentModuleName = null): bool
    {
        if (! config('tenant.enabled') || ! tenant()) {
            return true;
        }

        if ($parentModuleName !== null && ! tenant_has_module($parentModuleName)) {
            return false;
        }

        $rawNames = tenant('sub_module_names') ?: tenant('sub_modules') ?: [];
        if (empty($rawNames)) {
            try {
                $dbPlanSub = null;
                if (auth()->check() && auth()->user()->plan && !empty(auth()->user()->plan->sub_modules)) {
                    $dbPlanSub = auth()->user()->plan->sub_modules;
                } else {
                    $activePlan = \Illuminate\Support\Facades\DB::table('plans')->where('is_active', 1)->latest('id')->first();
                    if ($activePlan && !empty($activePlan->sub_modules)) {
                        $dbPlanSub = $activePlan->sub_modules;
                    }
                }
                if (!empty($dbPlanSub)) {
                    $rawNames = is_array($dbPlanSub) ? $dbPlanSub : (json_decode($dbPlanSub, true) ?: []);
                }
            } catch (\Throwable $e) {}
        }

        $normalize = function ($str) {
            return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$str));
        };

        $activeNames = array_map($normalize, (array) $rawNames);
        $activeSlugs = array_map('strtolower', (array) tenant('sub_module_slugs', []));

        // If no sub-modules are tracked at all in tenant payload, default to parent module status
        if (empty($activeNames) && empty($activeSlugs)) {
            return $parentModuleName ? tenant_has_module($parentModuleName) : true;
        }

        $checkList = is_array($subModuleNames) ? $subModuleNames : [$subModuleNames];
        foreach ($checkList as $item) {
            $norm = $normalize($item);
            $slug = strtolower(\Illuminate\Support\Str::slug($item));
            if (in_array($norm, $activeNames, true) || in_array($slug, $activeSlugs, true)) {
                return true;
            }
        }

        return false;
    }
}
