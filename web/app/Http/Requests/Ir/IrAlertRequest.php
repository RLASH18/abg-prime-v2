<?php

namespace App\Http\Requests\Ir;

use Illuminate\Foundation\Http\FormRequest;

class IrAlertRequest extends FormRequest
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
            'item_code'  => 'nullable|string',
            'alert_type' => 'required|string|in:unscanned,scanned',
            'notes'      => 'nullable|string|max:500',
        ];
    }
}
