<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Definir los campos que pueden ser asignados en masa
    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'payment_status',
        'shipping_address',
    ];

    // Relación con el usuario (un pedido pertenece a un usuario)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con los items del pedido (un pedido tiene muchos items)
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
