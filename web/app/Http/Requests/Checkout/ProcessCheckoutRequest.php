<?php

namespace App\Http\Requests\Checkout;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProcessCheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === UserRole::Customer;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => 'required|string|in:cash,gcash,bank_transfer',
            'delivery_method' => 'required|string|in:pickup,delivery',
            'delivery_address' => 'required_if:delivery_method,delivery|nullable|string|max:500',
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
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
            'delivery_method.required' => 'Please select a delivery method.',
            'delivery_method.in' => 'Invalid delivery method selected.',
            'delivery_address.required_if' => 'Delivery address is required for delivery orders.',
        ];
    }
}
