<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentDislike extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id', 'user_id',
    ];
}
