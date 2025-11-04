<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Group extends Model
{
    public function questions():HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function translations():HasMany
    {
        return $this->hasMany(GroupTranslation::class);
    }


    public function translation($lang = null): ?HasOne
    {
        $lang = $lang ?: app()->getLocale();
        $language = Language::query()->where('country_code', $lang)->first();

        if (!$language) {
            return null;
        }

        return $this->hasOne(GroupTranslation::class, 'group_id')->where('language_id', $language->id);
    }
}
