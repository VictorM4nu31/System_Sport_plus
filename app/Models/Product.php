<?php

namespace App\Models;

use App\Services\StripeProductService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return '$'.number_format($this->price, 2);
    }

    // Relación con las reseñas
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Relación con los items de pedido (para preservar el historial)
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating'), 1) ?: 0; // Retorna 0 si no hay reseñas
    }

    // Método para sincronizar manualmente con Stripe
    public function syncWithStripe()
    {
        $stripeService = new StripeProductService;

        return $stripeService->syncWithStripe($this);
    }

    // Verificar si el producto está sincronizado con Stripe
    public function isSyncedWithStripe()
    {
        return ! empty($this->stripe_product_id) && ! empty($this->stripe_price_id);
    }
}
