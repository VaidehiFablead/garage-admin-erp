<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateMaterialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'price'      => ['required', 'numeric', 'min:0'],
            'type_ids'   => ['required', 'array', 'min:1'],
            'type_ids.*' => [
                'required',
                'integer',
                Rule::exists('types', 'id')->where(function ($query) {
                    return $query->where('is_deleted', 0);
                }),
            ],
            'branch_id'  => ['nullable', 'numeric'],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'The material name is required.',
            'price.required'    => 'The price is required.',
            'price.numeric'     => 'The price must be a valid number.',
            'type_ids.required' => 'Please select at least one type.',
            'type_ids.array'    => 'The types must be an array of type IDs.',
            'type_ids.min'      => 'Please select at least one type.',
            'type_ids.*.exists' => 'One or more selected types do not exist or are deleted.',
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
