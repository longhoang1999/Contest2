<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'answer_text',
        'image_url',
        'video_url',
        'audio_url',
        'is_correct',
        'correct_answer',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

}