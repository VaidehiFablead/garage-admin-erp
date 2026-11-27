<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\Purchase\PurchaseBillVisionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PurchaseAiBillController extends Controller
{
    public function index(): View
    {
        $branchId = $this->branchId();
        $aiReady = app(PurchaseBillVisionService::class)->isConfigured();

        return view('purchase.ai-bill', [
            'vendors' => $this->vendors($branchId),
            'categories' => $this->categories($branchId),
            'products' => $this->products($branchId),
            'aiReady' => $aiReady,
        ]);
    }

    public function parse(Request $request, PurchaseBillVisionService $vision): JsonResponse
    {
        $request->validate([
            'bill' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        try {
            $extracted = $vision->extract($request->file('bill'));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $branchId = $this->branchId();
        $products = $this->products($branchId);
        $vendors = $this->vendors($branchId);
        $categories = $this->categories($branchId);

        $matchedVendor = $this->matchVendor($extracted['vendor_name'], $extracted['vendor_phone'], $vendors);
        $defaultCategory = $categories->first();

        $items = [];
        foreach ($extracted['items'] as $item) {
            $quantity = $this->stockQuantity($item['weight'], $item['bags']);
            $amount = (float) $item['amount'];
            if ($amount <= 0 && $item['rate'] > 0 && $quantity > 0) {
                $amount = round($item['rate'] * $quantity, 2);
            }

            $unitPrice = $quantity > 0 ? round($amount / $quantity, 4) : (float) $item['rate'];
            $match = $this->matchProduct($item['name'], $item['hsn'], $products);

            $items[] = [
                'name' => $item['name'],
                'hsn' => $item['hsn'],
                'bags' => $item['bags'],
                'weight' => $item['weight'],
                'rate' => $item['rate'],
                'amount' => $amount,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'product_id' => $match['id'] ?? null,
                'category_id' => $match['category_id'] ?? ($defaultCategory['id'] ?? null),
                'current_stock' => $match['quantity'] ?? 0,
                'action' => $match ? 'update' : 'create',
            ];
        }

        $grandTotal = (float) $extracted['grand_total'];
        $itemsTotal = round(array_sum(array_column($items, 'amount')), 2);
        if ($grandTotal <= 0) {
            $grandTotal = $itemsTotal;
        }

        return response()->json([
            'success' => true,
            'header' => [
                'vendor_id' => $matchedVendor['id'] ?? null,
                'vendor_name' => $extracted['vendor_name'],
                'vendor_phone' => $extracted['vendor_phone'],
                'vendor_address' => $extracted['vendor_address'],
                'bill_no' => $extracted['bill_no'],
                'bill_date' => $extracted['bill_date'],
                'customer_name' => $extracted['customer_name'],
                'grand_total' => $grandTotal,
                'items_total' => $itemsTotal,
                'amount_in_words' => $extracted['amount_in_words'],
            ],
            'items' => $items,
        ]);
    }

    private function branchId(): int
    {
        $user = Auth::user();
        $selectedSubAdminId = session('selectedSubAdminId');

        if ($user && $user->role === 'staff' && $user->branch_id) {
            return (int) $user->branch_id;
        }

        if ($user && $user->role === 'admin' && ! empty($selectedSubAdminId)) {
            return (int) $selectedSubAdminId;
        }

        return (int) ($user ? $user->id : 1);
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function vendors(int $branchId)
    {
        $hasBranch = Schema::hasColumn('users', 'branch_id');
        $hasIsDeleted = Schema::hasColumn('users', 'isDeleted');
        $hasRole = Schema::hasColumn('users', 'role');
        $hasPhone = Schema::hasColumn('users', 'phone');

        $query = User::query();
        if ($hasRole) {
            $query->where('role', 'vendor');
        }
        if ($hasIsDeleted) {
            $query->where('isDeleted', 0);
        }
        if ($hasBranch) {
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        $columns = ['id', 'name'];
        if ($hasPhone) {
            $columns[] = 'phone';
        }

        return $query->orderBy('name')
            ->get($columns)
            ->map(fn (User $vendor) => [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'phone' => $vendor->phone ?? '',
            ])
            ->values();
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function categories(int $branchId)
    {
        $hasBranch = Schema::hasColumn('categories', 'branch_id');
        $hasIsDeleted = Schema::hasColumn('categories', 'isDeleted');

        $query = Category::query();
        if ($hasIsDeleted) {
            $query->where('isDeleted', 0);
        }
        if ($hasBranch) {
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        return $query->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
            ])
            ->values();
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function products(int $branchId)
    {
        $hasBranch = Schema::hasColumn('products', 'branch_id');
        $hasStatus = Schema::hasColumn('products', 'status');
        $hasIsDeleted = Schema::hasColumn('products', 'isDeleted');
        $hasHsn = Schema::hasColumn('products', 'hsn_code');
        $hasQty = Schema::hasColumn('products', 'quantity');
        $hasPurchasePrice = Schema::hasColumn('products', 'purchase_price');

        $columns = ['id', 'name', 'category_id', 'price'];
        if ($hasHsn) {
            $columns[] = 'hsn_code';
        }
        if ($hasQty) {
            $columns[] = 'quantity';
        }
        if ($hasPurchasePrice) {
            $columns[] = 'purchase_price';
        }

        $query = Product::query();
        if ($hasIsDeleted) {
            $query->where('isDeleted', 0);
        }
        if ($hasStatus) {
            $query->where('status', 'active');
        }
        if ($hasBranch) {
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        return $query->orderBy('name')
            ->get($columns)
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'hsn_code' => $product->hsn_code ?? '',
                'quantity' => (float) ($product->quantity ?? 0),
                'price' => (float) ($product->purchase_price ?? $product->price ?? 0),
            ])
            ->values();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $vendors
     * @return array<string, mixed>|null
     */
    private function matchVendor(string $name, string $phone, $vendors): ?array
    {
        if ($phone !== '') {
            $byPhone = $vendors->first(function ($vendor) use ($phone) {
                return preg_replace('/\D+/', '', (string) $vendor['phone']) === $phone;
            });
            if ($byPhone) {
                return $byPhone;
            }
        }

        $needle = $this->normalizeName($name);
        if ($needle === '') {
            return null;
        }

        $best = null;
        $bestScore = 0;
        foreach ($vendors as $vendor) {
            similar_text($needle, $this->normalizeName((string) $vendor['name']), $percent);
            if ($percent > $bestScore) {
                $bestScore = $percent;
                $best = $vendor;
            }
        }

        return $bestScore >= 72 ? $best : null;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $products
     * @return array<string, mixed>|null
     */
    private function matchProduct(string $name, string $hsn, $products): ?array
    {
        $needle = $this->normalizeName($name);
        $hsn = trim($hsn);

        $best = null;
        $bestScore = 0;

        foreach ($products as $product) {
            $haystack = $this->normalizeName((string) $product['name']);
            $score = 0;
            similar_text($needle, $haystack, $percent);
            $score = (float) $percent;

            if ($needle !== '' && ($haystack !== '' && (str_contains($haystack, $needle) || str_contains($needle, $haystack)))) {
                $score = max($score, 88);
            }

            if ($hsn !== '' && trim((string) ($product['hsn_code'] ?? '')) === $hsn) {
                $score += 12;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $product;
            }
        }

        return $bestScore >= 78 ? $best : null;
    }

    private function stockQuantity(float $weight, float $bags): float
    {
        if ($weight > 0) {
            return $weight;
        }

        return $bags > 0 ? $bags : 1;
    }

    private function normalizeName(string $value): string
    {
        $value = mb_strtolower($value);
        $value = preg_replace('/[\p{Gujarati}\p{Devanagari}]+/u', ' ', $value) ?? $value;
        $value = preg_replace('/[^a-z0-9]+/i', ' ', $value) ?? $value;

        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }
}
