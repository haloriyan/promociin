<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCenterCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_center_id', 'title', 'description', 'is_online', 'is_certified', 'cover',
        'lessons_count', 'duration'
    ];
}
