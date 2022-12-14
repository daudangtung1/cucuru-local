<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    const GENDER = [
        'MALE' => 1,
        'FEMALE' => 0,
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'full_name',
        'gender',
        'avatar_url',
        'cover_url',
        'twitter_url',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'youtube_url',
        'amazon_url',
        'profile_info',
        'allow_set_post_sensitive_content',
        'allow_view_post_sensitive_content',
        'birth_day',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
