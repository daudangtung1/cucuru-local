<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    const ACTIVE = 1;
    const INACTIVE  = 0;
    const MALE = 1;
    const FEMALE = 0;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'created_by');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function plans()
    {
        return $this->hasMany(Plan::class, 'created_by', 'id');
    }

    public function follows()
    {
        return$this->hasManyThrough(self::class, Follow::class, 'user_id', 'id', 'id', 'follow_user_id');
    }

    public function followers()
    {
        return$this->hasManyThrough(self::class, Follow::class, 'follow_user_id', 'id', 'id', 'user_id');
    }

    public function notificationSetting() {
        return $this->hasOne(NotificationSetting::class);
    }
}
