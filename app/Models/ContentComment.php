<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'content_id', 'user_id', 'body', 'likes_count'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
