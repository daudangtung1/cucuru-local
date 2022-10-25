<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE = [
        'NORMAL' => 1,
        'MEMBER_ONLY' => 2,
        'SOLD_SEPARATELY' => 3,
    ];

    protected $fillable = [
        'type',
        'content',
        'plan_id',
        'is_adult',
        'created_by',
        'published_at',
    ];

    protected $appends  = ['medias'];

    protected $dates = ['created_at', 'updated_at', 'published_at', 'deleted_at'];

    public static function boot()
    {
        parent::boot();

        // TODO:Chỗ này mai sau dùng observer
        static::deleting(function ($post) {
            $post->medias()->delete();
            $post->comments()->delete();

            Storage::disk('s3')->deleteDirectory("/posts/$post->id");
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function medias()
    {
        return $this->morphMany(Media::class, 'mediaable');
    }

    public function getNumberMediaOfPostAttribute()
    {
        return $this->medias()->count();
    }

    public function getMediasAttribute()
    {
        return $this->medias()->get(['id', 'link', 'type']);
    }
}
