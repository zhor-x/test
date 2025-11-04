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

    public function translation($lang = null): ?HasOne
    {
        $lang = $lang ?: app()->getLocale();
        $language = Language::query()->where('country_code', $lang)->first();

        if (!$language) {
            return null;
        }
        return $this->hasOne(PddChapterTranslation::class, 'chapter_id')->where('language_id', $language->id);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(PddRule::class, 'chapter_id');
    }
}
