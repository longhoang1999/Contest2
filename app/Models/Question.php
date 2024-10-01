<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'question_text',
        'image_url',
        'video_url',
        'audio_url',
        'difficulty',
        'focus_level',
        'average_time',
        'correct_percentage',
        'note',
        'is_active',
        'is_demo',
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
