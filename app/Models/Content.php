<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'filename', 'caption', 'visibility', 'thumbnail', 'stream_id',
        'likes_count', 'dislikes_count', 'comments_count', 'views_count', 'tags', 'can_be_commented', 'can_be_shared',
        'industry_related',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function stream() {
        return $this->belongsTo(Stream::class, 'stream_id');
    }
    public function likes() {
        return $this->hasMany(ContentLike::class, 'content_id');
    }
    public function dislikes() {
        return $this->hasMany(ContentDislike::class, 'content_id');
    }
}
