<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name','email','password','photo','username','token', 'industry',
        'followers_count', 'following_count', 'likes_count', 'dislikes_count'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function skills() {
        return $this->hasMany(UserSkill::class, 'user_id');
    }
    public function experiences() {
        return $this->hasMany(UserExperience::class, 'user_id')->orderBy('start_date', 'DESC');
    }
    public function educations() {
        return $this->hasMany(Education::class, 'user_id')->orderBy('end_date', 'DESC');
    }
    public function certificates() {
        return $this->hasMany(UserCertificate::class, 'user_id')->orderBy('publish_date', 'DESC');
    }
    public function following_status() {
        return $this->hasOne(UserFollowing::class, 'following_user_id');
    }
    public function follower_status() {
        return $this->hasOne(UserFollowers::class, 'follower_user_id');
    }
}
