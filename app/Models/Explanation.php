<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Explanation extends Model
{
    protected $fillable = ['group_id', 'question_id'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ExplanationTranslation::class);
    }

    public function translation($lang = null): HasOne
    {
        $language = Language::resolveByCode($lang ?: app()->getLocale());
        $fallback = Language::fallback();

        $relation = $this->hasOne(ExplanationTranslation::class);

        return Language::applyTranslationScope($relation, $language, $fallback);
    }
}
