<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'skill_id', 'title', 'publish_date', 'expiry_date', 'publisher', 'url', 'filename'
    ];

    public function skill() {
        return $this->belongsTo(UserSkill::class, 'skill_id');
    }
}
