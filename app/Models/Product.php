<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Services\StripeProductService;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'model',
        'price',
        'description',
        'stock',
        'category_id',
        'image',
        'sizes',
        'colors',
        'material',
        'gender',
        'sport_type',
        'weight',
        'specifications',
        'is_featured',
        'sku',
        'stripe_product_id',
        'stripe_price_id',
    ];

    protected $casts = [
        'sizes' => 'array',
        'colors' => 'array',
        'specifications' => 'array',
        'is_featured' => 'boolean',
        'weight' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    // Nuevo método para reservas de stock
    public function reserveStock(int $quantity): bool
    {
        return DB::transaction(function () use ($quantity) {
            $this->lockForUpdate();
            if ($this->stock >= $quantity) {
                $this->decrement('stock', $quantity);
                return true;
            }
            return false;
        });
    }

    // Relación con las reseñas
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating'), 1) ?: 0; // Retorna 0 si no hay reseñas
    }

    // Eventos del modelo para sincronizar con Stripe
    protected static function booted()
    {
        // Crear producto en Stripe después de crearlo localmente
        static::created(function ($product) {
            if (config('stripe.auto_sync', true)) {
                $stripeService = new StripeProductService();
                $stripeService->createStripeProduct($product);
            }
        });

        // Actualizar producto en Stripe después de actualizarlo localmente
        static::updated(function ($product) {
            if (config('stripe.auto_sync', true) && $product->stripe_product_id) {
                $stripeService = new StripeProductService();
                $stripeService->updateStripeProduct($product);
            }
        });

        // Archivar producto en Stripe antes de eliminarlo localmente
        static::deleting(function ($product) {
            if (config('stripe.auto_sync', true) && $product->stripe_product_id) {
                $stripeService = new StripeProductService();
                $stripeService->deleteStripeProduct($product);
            }
        });
    }

    // Método para sincronizar manualmente con Stripe
    public function syncWithStripe()
    {
        $stripeService = new StripeProductService();
        return $stripeService->syncWithStripe($this);
    }

    // Verificar si el producto está sincronizado con Stripe
    public function isSyncedWithStripe()
    {
        return !empty($this->stripe_product_id) && !empty($this->stripe_price_id);
    }
}
