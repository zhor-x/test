<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RoadSignCategory extends Model
{
    public function translation($lang = null): ?HasOne
    {
        $lang = $lang ?: app()->getLocale();
        $language = Language::query()->where('country_code', $lang)->first();

        if (!$language) {
            return null;
        }
        return $this->hasOne(RoadSignCategoryTranslation::class, 'road_sign_category_id')->where('language_id', $language->id);
    }

    public function roadSings(): HasMany
    {
        return $this->hasMany(RoadSign::class, 'category_id');
    }


}
