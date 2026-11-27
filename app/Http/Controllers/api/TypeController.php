<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTypeRequest;
use App\Http\Requests\UpdateTypeRequest;
use App\Models\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TypeController extends Controller
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
     * Display a listing of the types.
     */
    public function index(Request $request): JsonResponse
    {
        $branchId = $this->resolveBranchId($request);

        $query = Type::where('branch_id', $branchId)
            ->where('is_deleted', 0)
            ->latest('id');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where('type_name', 'LIKE', "%{$search}%");
        }

        if ($request->boolean('all') || $request->get('paginate') === 'false') {
            $types = $query->get();

            return response()->json([
                'status' => true,
                'data'   => $types,
            ], 200);
        }

        $perPage = (int) $request->input('per_page', 10);
        $types = $query->paginate($perPage);

        return response()->json([
            'status'     => true,
            'data'       => $types->items(),
            'pagination' => [
                'current_page' => $types->currentPage(),
                'last_page'    => $types->lastPage(),
                'per_page'     => $types->perPage(),
                'total'        => $types->total(),
                'from'         => $types->firstItem(),
                'to'           => $types->lastItem(),
            ],
        ], 200);
    }

    /**
     * Store a newly created type.
     */
    public function store(StoreTypeRequest $request): JsonResponse
    {
        $branchId = $this->resolveBranchId($request);

        $type = Type::create([
            'type_name'  => $request->validated('type_name'),
            'branch_id'  => $branchId,
            'is_deleted' => 0,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Type created successfully.',
            'data'    => $type,
        ], 201);
    }

    /**
     * Display the specified type.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $branchId = $this->resolveBranchId($request);

        $type = Type::where('branch_id', $branchId)
            ->where('is_deleted', 0)
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $type,
        ], 200);
    }

    /**
     * Update the specified type.
     */
    public function update(UpdateTypeRequest $request, $id): JsonResponse
    {
        $branchId = $this->resolveBranchId($request);

        $type = Type::where('branch_id', $branchId)
            ->where('is_deleted', 0)
            ->findOrFail($id);

        $type->update([
            'type_name' => $request->validated('type_name'),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Type updated successfully.',
            'data'    => $type,
        ], 200);
    }

    /**
     * Remove the specified type (soft delete).
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $branchId = $this->resolveBranchId($request);

        $type = Type::where('branch_id', $branchId)
            ->where('is_deleted', 0)
            ->findOrFail($id);

        $type->update(['is_deleted' => 1]);

        return response()->json([
            'status'  => true,
            'message' => 'Type deleted successfully.',
        ], 200);
    }
}
