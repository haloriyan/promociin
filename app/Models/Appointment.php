<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id', 'employee_id', 'dues', 'skillsets',
        'is_accepted_by_employee', 'link', 'notes'
    ];

    public function employer() {
        return $this->belongsTo(User::class, 'employer_id');
    }
    public function employee() {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
