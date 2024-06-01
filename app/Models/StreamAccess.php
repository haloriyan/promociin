<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StreamAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'code', 'expiry', 'has_used'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
