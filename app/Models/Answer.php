<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Answer extends Model
{
    protected $fillable = ['is_right', 'group_id', 'question_id'];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function translation($lang = null): HasOne
    {
        $language = Language::resolveByCode($lang ?: app()->getLocale());
        $fallback = Language::fallback();

        $relation = $this->hasOne(AnswerTranslation::class, 'answer_id');

        return Language::applyTranslationScope($relation, $language, $fallback);
    }

    public function translations()
    {
        return $this->hasMany(AnswerTranslation::class, 'answer_id');
    }




}
