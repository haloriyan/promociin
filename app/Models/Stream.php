<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'stream_key', 'visibility'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
