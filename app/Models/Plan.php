<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'created_by',
        'monthly_fee',
        'description',
        'genre_id',
        'viewing_restriction',
        'set_back_number_sale',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function user() {
        return $this->belongsTo(User::class, 'created_by');
    }
}
