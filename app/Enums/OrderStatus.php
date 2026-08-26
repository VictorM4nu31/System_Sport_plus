<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';
    case FAILED = 'failed';
    case CANCELLED = 'cancelado';
    case COMPLETED = 'completado';
}
