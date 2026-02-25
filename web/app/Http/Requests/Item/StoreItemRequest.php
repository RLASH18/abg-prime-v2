<?php

namespace App\Http\Requests\Item;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreItemRequest extends FormRequest
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

    /**
     * Get custom validation messages
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'supplier_id.exists' => 'The selected supplier does not exist.',
            'brand_name.required' => 'Please enter the brand name.',
            'brand_name.max' => 'Brand name cannot exceed 255 characters.',
            'item_name.required' => 'Please enter the item name.',
            'item_name.max' => 'Item name cannot exceed 255 characters.',
            'category.required' => 'Please select a category.',
            'category.in' => 'Please select a valid category from the list.',
            'item_image_1.image' => 'Item image 1 must be an image file.',
            'item_image_1.mimes' => 'Item image 1 must be a file of type: jpeg, png, jpg, gif, or webp.',
            'item_image_1.max' => 'Item image 1 must not exceed 2MB.',
            'item_image_2.image' => 'Item image 2 must be an image file.',
            'item_image_2.mimes' => 'Item image 2 must be a file of type: jpeg, png, jpg, gif, or webp.',
            'item_image_2.max' => 'Item image 2 must not exceed 2MB.',
            'item_image_3.image' => 'Item image 3 must be an image file.',
            'item_image_3.mimes' => 'Item image 3 must be a file of type: jpeg, png, jpg, gif, or webp.',
            'item_image_3.max' => 'Item image 3 must not exceed 2MB.',
            'unit_price.required' => 'Please enter the unit price.',
            'unit_price.numeric' => 'Unit price must be a valid number.',
            'unit_price.min' => 'Unit price cannot be negative.',
            'quantity.required' => 'Please enter the quantity.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'restock_threshold.integer' => 'Restock threshold must be a whole number.',
            'restock_threshold.min' => 'Restock threshold must be at least 10.',
        ];
    }
}
