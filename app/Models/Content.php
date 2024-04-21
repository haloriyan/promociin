<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'filename', 'caption', 'visibility', 'thumbnail',
        'likes_count', 'comments_count', 'tags', 'can_be_commented', 'can_be_shared'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
