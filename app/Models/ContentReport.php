<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_by_user_id', 'content_id', 'topic', 'notes', 'resolved'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'report_by_user_id');
    }
    public function content() {
        return $this->belongsTo(Content::class, 'content_id');
    }
}
