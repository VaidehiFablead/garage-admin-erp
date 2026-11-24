<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class StaffDepartmentScope
{
    public const KEY_WAREHOUSE = 'warehouse';
    public const KEY_ACCOUNT = 'account';
    public const KEY_SALES = 'sales';

    public const REPORT_SALES = 34;
    public const REPORT_PURCHASE = 35;
    public const REPORT_EXPENSE = 36;
    public const REPORT_PROFIT_LOSS = 37;
    public const REPORT_TDS = 38;

    public static function departmentKey(?User $user): ?string
    {
        if (! $user || $user->role !== 'staff') {
            return null;
        }

        if (! $user->relationLoaded('userDetail')) {
            $user->load('userDetail.department');
        } elseif ($user->userDetail && ! $user->userDetail->relationLoaded('department')) {
            $user->userDetail->load('department');
        }

        $departmentName = optional($user->userDetail?->department)->department_name
            ?? optional($user->details?->department)->department_name;

        if (empty($departmentName)) {
            return null;
        }

        $normalized = strtolower(trim($departmentName));

        if (str_contains($normalized, 'warehouse')) {
            return self::KEY_WAREHOUSE;
        }

        if (str_contains($normalized, 'account') || str_contains($normalized, 'cashier')) {
            return self::KEY_ACCOUNT;
        }

        if (str_contains($normalized, 'sales')) {
            return self::KEY_SALES;
        }

        return 'other';
    }

    /** Warehouse and Account/Cashier see all branch records; everyone else sees own only. */
    public static function seesAllBranchOrders(?User $user): bool
    {
        return in_array(self::departmentKey($user), [self::KEY_WAREHOUSE, self::KEY_ACCOUNT], true);
    }

    public static function restrictToOwnRecords(?User $user): bool
    {
        return $user
            && $user->role === 'staff'
            && ! self::seesAllBranchOrders($user);
    }

    public static function showProductsDelivery(?User $user): bool
    {
        return self::departmentKey($user) === self::KEY_WAREHOUSE;
    }

    public static function applyOrderScope($query, User $user, $selectedSubAdminId = null): void
    {
        $userId = $user->id;
        $role = $user->role;

        if ($role === 'sub-admin') {
            $query->where('branch_id', $userId);

            return;
        }

        if ($role === 'admin' && ! empty($selectedSubAdminId)) {
            $query->where('branch_id', $selectedSubAdminId);

            return;
        }

        if ($role === 'staff') {
            if (self::restrictToOwnRecords($user)) {
                $query->where(function ($q) use ($userId) {
                    $q->where('created_by', $userId)
                        ->orWhere('staff_id', $userId);
                });
            } elseif (! empty($user->branch_id)) {
                $query->where('branch_id', $user->branch_id);
            }

            return;
        }

        $query->where('branch_id', $userId);
    }

    public static function resolveBranchId(User $user, $selectedSubAdminId = null): int
    {
        if ($user->role === 'staff' && $user->branch_id) {
            return (int) $user->branch_id;
        }

        if ($user->role === 'sub-admin') {
            return (int) $user->id;
        }

        if ($user->role === 'admin' && ! empty($selectedSubAdminId)) {
            return (int) $selectedSubAdminId;
        }

        return (int) $user->id;
    }

    public static function applyPurchaseCreatedByScope($query, User $user, string $column = 'created_by'): void
    {
        if ($user->role !== 'staff' || ! self::restrictToOwnRecords($user)) {
            return;
        }

        $query->where($column, $user->id);
    }

    public static function applyPurchaseEloquentScope($query, User $user, $selectedSubAdminId = null): void
    {
        $query->where('branch_id', self::resolveBranchId($user, $selectedSubAdminId));

        if ($user->role === 'staff' && self::restrictToOwnRecords($user)) {
            $query->whereHas('invoice', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        }
    }

    public static function applyStaffCreatedByScope($query, User $user, string $column = 'created_by'): void
    {
        if ($user->role !== 'staff' || ! self::restrictToOwnRecords($user)) {
            return;
        }

        $query->where($column, $user->id);
    }

    public static function applyPurchaseInvoiceScope($query, User $user, $selectedSubAdminId = null): void
    {
        $query->where('branch_id', self::resolveBranchId($user, $selectedSubAdminId));
        self::applyStaffCreatedByScope($query, $user);
    }

    /** Scope models linked to orders (returns, credit notes, etc.) for restricted staff. */
    public static function applyOrderRelatedScope($query, User $user, string $orderRelation = 'order'): void
    {
        if ($user->role !== 'staff' || ! self::restrictToOwnRecords($user)) {
            return;
        }

        $query->whereHas($orderRelation, function ($orderQuery) use ($user) {
            $orderQuery->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhere('staff_id', $user->id);
            });
        });
    }

    public static function applyRestrictedOrderJoinScope($query, User $user, string $ordersAlias = 'orders'): void
    {
        if ($user->role !== 'staff' || ! self::restrictToOwnRecords($user)) {
            return;
        }

        $query->where(function ($q) use ($user, $ordersAlias) {
            $q->where("{$ordersAlias}.created_by", $user->id)
                ->orWhere("{$ordersAlias}.staff_id", $user->id);
        });
    }

    public static function applyRestrictedPurchaseJoinScope($query, User $user, string $purchasesAlias = 'purchases', string $invoiceAlias = 'purchase_invoice'): void
    {
        if ($user->role !== 'staff' || ! self::restrictToOwnRecords($user)) {
            return;
        }

        $query->where(function ($q) use ($user, $purchasesAlias, $invoiceAlias) {
            $q->where("{$purchasesAlias}.created_by", $user->id)
                ->orWhere("{$invoiceAlias}.created_by", $user->id);
        });
    }

    /** Staff report modules: View = report access, Add = show chart/graph. */
    public static function canShowReportChart(int $moduleId, ?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (! $user || $user->role !== 'staff') {
            return true;
        }

        return (bool) app('hasPermission')($moduleId, 'add');
    }

    public static function buildDashboardDeliveries(User $user, int $limit = 20)
    {
        $query = Order::with(['creator:id,name', 'staff:id,name', 'deliveries.deliveredBy', 'user'])
            ->where('isDeleted', '!=', 1)
            ->where('order_type', 'delivery');

        if (Schema::hasColumn('orders', 'quotation_status')) {
            $query->where('quotation_status', '!=', 'quotation');
        }

        self::applyOrderScope($query, $user);

        return $query->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                $latestDelivery = $order->deliveries
                    ->sortByDesc('created_at')
                    ->first();

                return (object) [
                    'id' => $latestDelivery?->id,
                    'order_id' => $order->id,
                    'order' => $order,
                    'status' => $latestDelivery?->status ?: ($order->delivery_status ?: 'pending'),
                    'deliveredBy' => $latestDelivery?->deliveredBy ?: ($order->staff ?: $order->creator),
                    'delivered_by' => $latestDelivery?->delivered_by ?: ($order->staff_id ?: $order->created_by),
                    'has_delivery_record' => (bool) $latestDelivery,
                ];
            });
    }
}
