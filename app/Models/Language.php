<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'languages';

    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'file',
        'value',
    ];

    protected $dates = ['created_at', 'updated_at'];
}
