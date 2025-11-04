<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Product as StripeProduct;
use Stripe\Price as StripePrice;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class StripeProductService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * Crear producto en Stripe cuando se crea localmente
     */
    public function createStripeProduct(Product $product)
    {
        try {
            // Crear el producto en Stripe
            $stripeProduct = StripeProduct::create([
                'name' => $product->name,
                'description' => $product->description,
                'metadata' => [
                    'local_product_id' => $product->id,
                    'sku' => $product->sku ?? '',
                    'brand' => $product->brand ?? '',
                    'model' => $product->model ?? '',
                    'sport_type' => $product->sport_type ?? '',
                    'gender' => $product->gender ?? '',
                    'material' => $product->material ?? '',
                ],
                'images' => $product->image ? [asset('storage/products/' . $product->image)] : [],
            ]);

            // Crear el precio en Stripe
            $stripePrice = StripePrice::create([
                'product' => $stripeProduct->id,
                'unit_amount' => $product->price * 100, // Stripe maneja centavos
                'currency' => 'mxn',
                'metadata' => [
                    'local_product_id' => $product->id,
                ]
            ]);

            // Actualizar el producto local con los IDs de Stripe
            $product->update([
                'stripe_product_id' => $stripeProduct->id,
                'stripe_price_id' => $stripePrice->id,
            ]);

            Log::info('Producto creado en Stripe', [
                'local_product_id' => $product->id,
                'stripe_product_id' => $stripeProduct->id,
                'stripe_price_id' => $stripePrice->id,
            ]);

            return [
                'success' => true,
                'stripe_product' => $stripeProduct,
                'stripe_price' => $stripePrice,
            ];

        } catch (\Exception $e) {
            Log::error('Error creando producto en Stripe', [
                'local_product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Actualizar producto en Stripe cuando se actualiza localmente
     */
    public function updateStripeProduct(Product $product)
    {
        try {
            if (!$product->stripe_product_id) {
                // Si no tiene ID de Stripe, crear el producto
                return $this->createStripeProduct($product);
            }

            // Actualizar el producto en Stripe
            $stripeProduct = StripeProduct::update($product->stripe_product_id, [
                'name' => $product->name,
                'description' => $product->description,
                'metadata' => [
                    'local_product_id' => $product->id,
                    'sku' => $product->sku ?? '',
                    'brand' => $product->brand ?? '',
                    'model' => $product->model ?? '',
                    'sport_type' => $product->sport_type ?? '',
                    'gender' => $product->gender ?? '',
                    'material' => $product->material ?? '',
                ],
                'images' => $product->image ? [asset('storage/products/' . $product->image)] : [],
            ]);

            // Si el precio cambió, crear un nuevo precio (Stripe no permite actualizar precios)
            if ($product->isDirty('price') && $product->stripe_price_id) {
                // Desactivar el precio anterior
                StripePrice::update($product->stripe_price_id, [
                    'active' => false,
                ]);

                // Crear nuevo precio
                $stripePrice = StripePrice::create([
                    'product' => $product->stripe_product_id,
                    'unit_amount' => $product->price * 100,
                    'currency' => 'mxn',
                    'metadata' => [
                        'local_product_id' => $product->id,
                    ]
                ]);

                $product->update(['stripe_price_id' => $stripePrice->id]);
            }

            Log::info('Producto actualizado en Stripe', [
                'local_product_id' => $product->id,
                'stripe_product_id' => $product->stripe_product_id,
            ]);

            return [
                'success' => true,
                'stripe_product' => $stripeProduct,
            ];

        } catch (\Exception $e) {
            Log::error('Error actualizando producto en Stripe', [
                'local_product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Eliminar producto de Stripe cuando se elimina localmente
     */
    public function deleteStripeProduct(Product $product)
    {
        try {
            if ($product->stripe_product_id) {
                // Archivar el producto en Stripe (no se puede eliminar completamente)
                StripeProduct::update($product->stripe_product_id, [
                    'active' => false,
                ]);

                Log::info('Producto archivado en Stripe', [
                    'local_product_id' => $product->id,
                    'stripe_product_id' => $product->stripe_product_id,
                ]);
            }

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('Error archivando producto en Stripe', [
                'local_product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Sincronizar producto existente con Stripe
     */
    public function syncWithStripe(Product $product)
    {
        if ($product->stripe_product_id) {
            return $this->updateStripeProduct($product);
        } else {
            return $this->createStripeProduct($product);
        }
    }

    /**
     * Obtener información del producto desde Stripe
     */
    public function getStripeProduct(Product $product)
    {
        try {
            if (!$product->stripe_product_id) {
                return ['success' => false, 'error' => 'Producto no tiene ID de Stripe'];
            }

            $stripeProduct = StripeProduct::retrieve($product->stripe_product_id);
            $stripePrice = $product->stripe_price_id ? StripePrice::retrieve($product->stripe_price_id) : null;

            return [
                'success' => true,
                'stripe_product' => $stripeProduct,
                'stripe_price' => $stripePrice,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
