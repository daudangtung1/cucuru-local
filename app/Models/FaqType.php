<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqType extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function faqs() {
        return $this->hasMany(Faq::class);
    }
}
