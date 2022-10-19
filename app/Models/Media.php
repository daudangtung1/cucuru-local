<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;

    const MIMETYPE = [
        'image' => 1,
        'video' => 2,
    ];

    protected $table = 'medias';

    protected $fillable = [
        'link',
        'type',
        'mediaable_type',
        'mediaable_id',
    ];

    protected $dates = ['created_at', 'updated_at', 'published_at', 'deleted_at'];

    public function getTypeAttribute($value)
    {
        return array_flip(self::MIMETYPE)[$value];
    }
}
