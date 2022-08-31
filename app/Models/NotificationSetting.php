<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'email_notification',
        'comment_notification',
        'reply_notification',
        'follow_notification',
        'join_fan_club_notification',
        'tip_notification',
        'post_video_compression_notification',
        'following_creator_post_notification',
        'subscribing_creator_post_notification',
        'in_site_notification',
        'cancel_of_plan_notification',
    ];

    protected $dates = ['created_at', 'updated_at'];
}
