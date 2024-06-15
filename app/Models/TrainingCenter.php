<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'cover', 'icon', 'address', 'phone', 'email', 'center', 'is_approved', 'country', 'website'
    ];

    public function courses() {
        return $this->hasMany(TrainingCenterCourse::class, 'training_center_id');
    }
}
