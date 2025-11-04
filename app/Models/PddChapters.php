<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PddChapters extends Model
{
    protected $fillable = [
        'title',
    ];

    public function translation($lang = null): HasOne
    {
        $language = Language::resolveByCode($lang ?: app()->getLocale());
        $fallback = Language::fallback();

        $relation = $this->hasOne(PddChapterTranslation::class, 'chapter_id');

        return Language::applyTranslationScope($relation, $language, $fallback);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(PddRule::class, 'chapter_id');
    }
}
