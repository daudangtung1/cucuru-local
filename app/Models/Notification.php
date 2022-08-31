<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'is_important',
    ];

    protected $dates = ['created_at', 'updated_at', 'read_at'];
}
