<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    /**
     * Resolve the active branch ID based on the authenticated user's role.
     */
    private function resolveBranchId(Request $request): int
    {
        $user = $request->user() ?? Auth::guard('api')->user() ?? auth()->user();
        if (!$user) {
            return (int) ($request->branch_id ?? 0);
        }

        $role = strtolower($user->role ?? '');

        return match ($role) {
            'sub-admin' => (int) $user->id,
            'staff'     => (int) ($user->branch_id ?: $user->id),
            'admin'     => (int) ($request->selectedSubAdminId ?: ($request->sub_branch_id ?: ($request->branch_id ?: (session('selectedSubAdminId') ?: $user->id)))),
            default     => (int) ($user->branch_id ?: $user->id),
        };
    }

    /**
     * Display a listing of materials with their associated types.
     */
    public function index(Request $request): JsonResponse
    {
        $branchId = $this->resolveBranchId($request);

        $query = Material::where('branch_id', $branchId)
            ->where('is_deleted', 0)
            ->with(['types' => function ($q) {
                $q->select('types.id', 'types.type_name')
                  ->where('types.is_deleted', 0);
            }])
            ->latest('id');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('types', function ($tq) use ($search) {
                      $tq->where('types.is_deleted', 0)
                         ->where('type_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type_id')) {
            $typeId = (int) $request->type_id;
            $query->whereHas('types', function ($tq) use ($typeId) {
                $tq->where('types.id', $typeId)
                   ->where('types.is_deleted', 0);
            });
        }

        if ($request->boolean('all') || $request->get('paginate') === 'false') {
            $materials = $query->get();

            return response()->json([
                'status' => true,
                'data'   => $materials,
            ], 200);
        }

        $perPage = (int) $request->input('per_page', 10);
        $materials = $query->paginate($perPage);

        return response()->json([
            'status'     => true,
            'data'       => $materials->items(),
            'pagination' => [
                'current_page' => $materials->currentPage(),
                'last_page'    => $materials->lastPage(),
                'per_page'     => $materials->perPage(),
                'total'        => $materials->total(),
                'from'         => $materials->firstItem(),
                'to'           => $materials->lastItem(),
            ],
        ], 200);
    }

    /**
     * Store a newly created material and sync its types.
     */
    public function store(StoreMaterialRequest $request): JsonResponse
    {
        $branchId = $this->resolveBranchId($request);

        $material = DB::transaction(function () use ($request, $branchId) {
            $material = Material::create([
                'name'       => $request->validated('name'),
                'price'      => $request->validated('price'),
                'branch_id'  => $branchId,
                'is_deleted' => 0,
            ]);

            if ($request->has('type_ids')) {
                $material->types()->sync($request->validated('type_ids'));
            }

            return $material;
        });

        $material->load(['types' => function ($q) {
            $q->select('types.id', 'types.type_name');
        }]);

        return response()->json([
            'status'  => true,
            'message' => 'Material created successfully.',
            'data'    => $material,
        ], 201);
    }

    /**
     * Display the specified material with its associated types.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $branchId = $this->resolveBranchId($request);

        $material = Material::where('branch_id', $branchId)
            ->where('is_deleted', 0)
            ->with(['types' => function ($q) {
                $q->select('types.id', 'types.type_name')
                  ->where('types.is_deleted', 0);
            }])
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $material,
        ], 200);
    }

    /**
     * Update the specified material and its associated types.
     */
    public function update(UpdateMaterialRequest $request, $id): JsonResponse
    {
        $branchId = $this->resolveBranchId($request);

        $material = Material::where('branch_id', $branchId)
            ->where('is_deleted', 0)
            ->findOrFail($id);

        DB::transaction(function () use ($material, $request) {
            $material->update([
                'name'  => $request->validated('name'),
                'price' => $request->validated('price'),
            ]);

            if ($request->has('type_ids')) {
                $material->types()->sync($request->validated('type_ids'));
            }
        });

        $material->load(['types' => function ($q) {
            $q->select('types.id', 'types.type_name');
        }]);

        return response()->json([
            'status'  => true,
            'message' => 'Material updated successfully.',
            'data'    => $material,
        ], 200);
    }

    /**
     * Remove the specified material (soft delete).
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $branchId = $this->resolveBranchId($request);

        $material = Material::where('branch_id', $branchId)
            ->where('is_deleted', 0)
            ->findOrFail($id);

        $material->update(['is_deleted' => 1]);

        return response()->json([
            'status'  => true,
            'message' => 'Material deleted successfully.',
        ], 200);
    }
}
