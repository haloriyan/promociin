<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'salary', 'location', 'type', 'industry', 'expiry_date'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
