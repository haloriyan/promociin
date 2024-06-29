<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'cover', 'price', 'location', 'country'
    ];

    public function packages() {
        return $this->hasMany(ServicePackage::class, 'service_id');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
