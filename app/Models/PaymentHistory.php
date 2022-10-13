<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;

    const SUCCESS = 1;
    const FALSE  = 0;

    protected $fillable = [
        'plan_id',
        'payment_user_id',
        'status',
        'stripe_payment_id'
    ];
}
