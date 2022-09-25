<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'title',
        'content',
        'faq_type_id',
    ];

    public function filterTitle($query, $value)
    {
        return $query->where('title', 'like', '%' . $value . '%');
    }

    public function faqType()
    {
        return $this->belongsTo(FaqType::class);
    }
}
