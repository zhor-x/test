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

        return $this->hasOne(AnswerTranslation::class, 'answer_id')
            ->when($language, function ($query) use ($language) {
                $query->where('language_id', $language->id);
            });
    }

    public function translations()
    {
        return $this->hasMany(AnswerTranslation::class, 'answer_id');
    }




}
