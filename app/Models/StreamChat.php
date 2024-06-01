<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StreamChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'stream_id', 'type', 'body'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
