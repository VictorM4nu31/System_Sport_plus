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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

   
}
