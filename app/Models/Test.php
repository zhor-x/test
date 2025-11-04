<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Test extends Model
{
    use SoftDeletes;

    protected $fillable = ['duration', 'max_wrong_answers', 'is_valid'];

    public function translation(): ?HasOne
    {
        $lang = app()->getLocale();
        $language = Language::query()->where('country_code', $lang)->first();

        if (!$language) {
            return null;
        }

        return $this->hasOne(TestTranslation::class)->where('language_id', $language->id);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'test_questions', 'test_id', 'question_id')
            ->withPivot('id')
            ->withTimestamps();
    }


    public function userExamTests()
    {
        return $this->hasMany(UserExamTest::class);
    }
}
