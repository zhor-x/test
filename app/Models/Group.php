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


    public function translation($lang = null): HasOne
    {
        $language = Language::resolveByCode($lang ?: app()->getLocale());

        return $this->hasOne(GroupTranslation::class, 'group_id')
            ->when($language, function ($query) use ($language) {
                $query->where('language_id', $language->id);
            });
    }
}
