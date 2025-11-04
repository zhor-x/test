<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class QuestionTranslation extends Model
{
    protected $fillable = ['question_id', 'language_id', 'title'];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function getSanitizedTitle($title)
    {
        $text = mb_strtolower($title, 'UTF-8');
        return preg_replace('/[^\p{L}\p{N}]/u', '', $text);
    }
}
