<?php

namespace App\Http\Requests\Inventory;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreInventoryRequest extends FormRequest
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
            'supplier_id' => 'nullable|exists:suppliers,id',
            'brand_name' => 'required|string|max:255',
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:Hand Tools,Power Tools,Construction Materials,Locks and Security,Plumbing,Electrical,Paint and Finishes,Chemicals',
            'item_image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'item_image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'item_image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'restock_threshold' => 'nullable|integer|min:10',
        ];
    }
}
