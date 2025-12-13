<?php

namespace App\Http\Requests\DamagedItem;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreDamagedItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === UserRole::Admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string|max:500',
            'discount' => 'nullable|numeric|min:0',
            'status' => 'required|in:resellable,disposed',
        ];
    }

    /**
     * Get custom validation messages
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'item_id.required' => 'Please select an item.',
            'item_id.exists' => 'The selected item does not exist.',
            'quantity.required' => 'Please enter the damaged quantity.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'remarks.max' => 'Remarks cannot exceed 500 characters.',
            'discount.numeric' => 'Discount must be a valid number.',
            'discount.min' => 'Discount cannot be negative.',
            'status.required' => 'Please select a status.',
            'status.in' => 'Status must be either resellable or disposed.',
        ];
    }
}
