<?php

namespace App\Http\Requests\Delivery;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateDeliveryStatusRequest extends FormRequest
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
            'status' => 'required|in:scheduled,rescheduled,in_transit,delivered,failed',
            'driver_name' => 'nullable|string|max:100',
            'scheduled_date' => 'nullable|date|after_or_equal:today',
            'remarks' => 'nullable|string|max:500',
            'proof_of_delivery' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
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
            'status.required' => 'Please select a delivery status.',
            'status.in' => 'Invalid delivery status selected.',
            'driver_name.max' => 'Driver name cannot exceed 100 characters.',
            'scheduled_date.date' => 'Please enter a valid date.',
            'scheduled_date.after_or_equal' => 'Scheduled date cannot be in the past.',
            'remarks.max' => 'Remarks cannot exceed 500 characters.',
            'proof_of_delivery.image' => 'Proof of delivery must be an image file.',
            'proof_of_delivery.mimes' => 'Proof of delivery must be a file of type: jpeg, png, jpg, or webp.',
            'proof_of_delivery.max' => 'Proof of delivery image must not exceed 2MB.',
        ];
    }
}
