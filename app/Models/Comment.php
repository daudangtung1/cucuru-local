<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    const COMMENT_TYPE = [
        'POST' => 1,
    ];

    protected $fillable = [
        'content',
        'created_by',
        'commentable_id',
        'commentable_type',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function commentable()
    {
        return $this->morphTo();
    }
}
