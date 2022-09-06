<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'faq_type_id',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function faqType()
    {
        return $this->belongsTo(FaqType::class);
    }
}
