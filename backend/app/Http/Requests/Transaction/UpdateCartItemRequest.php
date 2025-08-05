<?php

namespace App\Http\Requests\Transaction;

use App\Http\Requests\BaseRequest;

class UpdateCartItemRequest extends BaseRequest
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
            'cart_id' => ['required', 'exists:carts,id'],
            'items' => ['required', 'array'],
            'items.*.food_id' => ['required', 'exists:food,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
    
    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'cart_id.required' => 'The cart ID is required.',
            'cart_id.exists' => 'The selected cart ID does not exist.',
            'items.required' => 'The items list is required.',
            'items.array' => 'The items must be an array.',
            'items.*.food_id.required' => 'Each cart item must have a food ID.',
            'items.*.food_id.exists' => 'One or more food IDs do not exist.',
            'items.*.quantity.required' => 'Each cart item must have a quantity.',
            'items.*.quantity.integer' => 'The quantity must be an integer.',
            'items.*.quantity.min' => 'The quantity must be at least 1.',
        ];
    }
}

