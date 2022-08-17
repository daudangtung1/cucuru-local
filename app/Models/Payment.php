<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    const PAYMENT_STATUS = [
        'FAIL' => 0,
        'SUCCESS' => 1,
        'PENDING' => 2,
    ];

    protected $fillable = [
        'amount',
        'origin_amount',
        'coupon_id',
        'status',
        'description',
        'payment_card_id',
        'created_by',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function paymentCard()
    {
        return $this->belongsTo(PaymentCard::class);
    }
}
