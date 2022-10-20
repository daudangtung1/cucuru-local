<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    const IMAGE_TYPE = 1;
    const THUMBNAIL_BLUR_TYPE = 2;
    const THUMBNAIL_ORIGIN_TYPE = 3;
    const VIDEO_TYPE = 4;
    const MIMETYPE = [
        'image' => 1,
        'video' => 2,
    ];

    protected $table = 'medias';

    protected $fillable = [
        'link',
        'type',
        'disk',
        'size',
        'mime_type',
        'mediaable_id',
        'mediaable_type',
    ];

    protected $dates = ['created_at', 'updated_at', 'published_at', 'deleted_at'];

    public function getTypeAttribute($value)
    {
        return array_flip(self::MIMETYPE)[$value];
    }
}
