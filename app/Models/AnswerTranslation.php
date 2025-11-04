<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerTranslation extends Model
{
    protected $fillable = ['answer_id', 'language_id', 'title'];

    public function answer()
    {
        return $this->belongsTo(Answer::class, 'answer_id');
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
