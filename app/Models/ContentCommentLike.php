<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentCommentLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id', 'content_id', 'user_id'
    ];
}
