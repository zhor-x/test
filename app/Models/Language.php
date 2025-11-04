<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['flag', 'country_code', 'country'];

    public function examTestTranslations()
    {
        return $this->hasMany(TestTranslation::class);
    }

    public function questionTranslations()
    {
        return $this->hasMany(QuestionTranslation::class);
    }

    public function answerTranslations()
    {
        return $this->hasMany(AnswerTranslation::class);
    }

    public function explanationTranslations()
    {
        return $this->hasMany(ExplanationTranslation::class);
    }

    public function groupTranslations()
    {
        return $this->hasMany(GroupTranslation::class);
    }
}
