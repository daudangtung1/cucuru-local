<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    const ACTIVE = 1;
    const INACTIVE = 0;

    protected $fillable = [
        'title',
        'status',
        'content',
        'created_by',
    ];

    protected $dates = ['created_at', 'updated_at', 'published_at', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'created_by');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
