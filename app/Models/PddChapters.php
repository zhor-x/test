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

        return $this->hasOne(PddChapterTranslation::class, 'chapter_id')
            ->when($language, function ($query) use ($language) {
                $query->where('language_id', $language->id);
            });
    }

    public function rules(): HasMany
    {
        return $this->hasMany(PddRule::class, 'chapter_id');
    }
}
