<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    const COMMENT_TYPE = [
        'post' => 1,
        'comment' => 2,
    ];

    protected $fillable = [
        'content',
        'user_id',
        'commentable_id',
        'commentable_type',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($comment) {
            $comment->comments()->delete();
        });
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'commentable_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
