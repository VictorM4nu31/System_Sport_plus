<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ValidationService
{
    /**
     * Common validation rules for order status.
     *
     * Alineadas con App\Enums\OrderStatus: el admin solo puede fijar
     * estados reales del flujo (más los históricos en español).
     */
    public static function orderStatusRules(): array
    {
        return [
            'status' => 'required|string|in:pending,paid,confirmed,rejected,failed,cancelado,completado',
        ];
    }

    /**
     * Common validation rules for product creation/update
     */
    public static function productRules(?int $productId = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0.01',
            'description' => 'required|string|min:10',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => $productId ? 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'sizes' => 'nullable|array',
            'sizes.*' => 'string|max:10',
            'colors' => 'nullable|array',
            'colors.*' => 'string|max:50',
            'material' => 'nullable|string|max:100',
            'gender' => 'nullable|in:hombre,mujer,unisex',
            'sport_type' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|array',
            'is_featured' => 'boolean',
            'sku' => 'nullable|string|max:50|unique:products,sku,'.$productId,
        ];
    }

    /**
     * Common validation rules for user creation/update
     */
    public static function userRules(?int $userId = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'password' => $userId ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Common validation rules for categories
     */
    public static function categoryRules(?int $categoryId = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($categoryId),
            ],
            'description' => 'nullable|string',
        ];
    }

    /**
     * Common validation rules for reviews
     */
    public static function reviewRules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
        ];
    }

    /**
     * Common validation rules for addresses
     */
    public static function addressRules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'state' => 'required|string|max:255',
            'municipality' => 'required|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'interior_number' => 'nullable|string|max:20',
            'contact_phone' => 'required|string|max:20',
            'additional_instructions' => 'nullable|string|max:500',
            'address_type' => 'nullable|string|in:envio,facturacion',
        ];
    }

    /**
     * Validate request with given rules
     */
    public static function validateRequest(Request $request, array $rules): array
    {
        return $request->validate($rules);
    }
}
