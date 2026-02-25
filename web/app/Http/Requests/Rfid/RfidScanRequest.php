<?php

namespace App\Http\Requests\Rfid;

use Illuminate\Foundation\Http\FormRequest;

class RfidScanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Middleware handles authorization
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item_code' => 'required|string',
            'action' => 'required|string|in:add,deduct',
            'quantity' => 'required|integer|min:1'
        ];
    }
}
