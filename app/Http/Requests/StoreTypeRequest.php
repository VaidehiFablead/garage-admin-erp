<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Resolve the active branch ID for unique validation.
     */
    protected function getBranchId(): int
    {
        $user = $this->user() ?? Auth::guard('api')->user() ?? auth()->user();
        if (!$user) {
            return (int) ($this->input('branch_id') ?? 0);
        }

        $role = strtolower($user->role ?? '');

        return match ($role) {
            'sub-admin' => (int) $user->id,
            'staff'     => (int) ($user->branch_id ?: $user->id),
            'admin'     => (int) ($this->input('selectedSubAdminId') ?: ($this->input('sub_branch_id') ?: ($this->input('branch_id') ?: (session('selectedSubAdminId') ?: $user->id)))),
            default     => (int) ($user->branch_id ?: $user->id),
        };
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('types', 'type_name')
                    ->where(function ($query) {
                        return $query->where('branch_id', $this->getBranchId())
                            ->where('is_deleted', 0);
                    }),
            ],
            'branch_id' => ['nullable', 'numeric'],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'type_name.required' => 'The type name is required.',
            'type_name.unique'   => 'The type name has already been taken in this branch.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'status'  => false,
            'message' => 'Validation error',
            'errors'  => $validator->errors(),
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
