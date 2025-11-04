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

    public function translation($lang = null): ?HasOne
    {

        $lang = $lang ?: app()->getLocale();
        $language = Language::query()->where('country_code', $lang)->first();

        if (!$language) {
            return null;
        }

        return $this->hasOne(AnswerTranslation::class, 'answer_id')->where('language_id', $language->id);
    }

    public function translations()
    {
        return $this->hasMany(AnswerTranslation::class, 'answer_id');
    }




}
