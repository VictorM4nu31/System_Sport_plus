<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'postal_code',
        'state',
        'municipality',
        'neighborhood',
        'street',
        'number',
        'interior_number',
        'contact_phone',
        'additional_instructions',
        'is_default',
        'address_type',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para obtener la dirección por defecto de un usuario
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope para obtener direcciones por tipo
     */
    public function scopeByType($query, $type)
    {
        return $query->where('address_type', $type);
    }

    /**
     * Establecer esta dirección como la por defecto para el usuario
     */
    public function setAsDefault()
    {
        // Primero, quitar el estado de default de todas las direcciones del usuario
        static::where('user_id', $this->user_id)->update(['is_default' => false]);

        // Luego, establecer esta dirección como default
        $this->update(['is_default' => true]);
    }
}
