<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacancyApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacancy_id', 'user_id'
    ];

    public function vacancy() {
        return $this->belongsTo(Vacancy::class, 'vacancy_id');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
