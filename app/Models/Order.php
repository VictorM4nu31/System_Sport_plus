<?php

namespace App\Models;

use App\Enums\OrderStatus;
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
        'shipping_address_id',
        'payment_intent_id',
        'notes',
        'rejection_reason',
        'rejected_at',
        'rejected_by',
    ];

    // Relación con el usuario (un pedido pertenece a un usuario)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con la dirección de envío
    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    // Mantener método address() para compatibilidad
    public function address()
    {
        return $this->shippingAddress();
    }

    // Relación con los items del pedido (un pedido tiene muchos items)
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relación con el usuario que rechazó el pedido
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Verificar si el pedido fue rechazado
    public function isRejected()
    {
        return $this->status === OrderStatus::REJECTED->value;
    }

    // Obtener la fecha de rechazo formateada
    public function getRejectedAtFormattedAttribute()
    {
        return $this->rejected_at ? $this->rejected_at->format('d/m/Y H:i') : null;
    }
}
