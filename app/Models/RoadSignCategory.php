<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RoadSignCategory extends Model
{
    public function translation($lang = null): HasOne
    {
        $language = Language::resolveByCode($lang ?: app()->getLocale());
        $fallback = Language::fallback();

        $relation = $this->hasOne(RoadSignCategoryTranslation::class, 'road_sign_category_id');

        return Language::applyTranslationScope($relation, $language, $fallback);
    }

    public function roadSings(): HasMany
    {
        return $this->hasMany(RoadSign::class, 'category_id');
    }


}
