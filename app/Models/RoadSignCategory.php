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

        return $this->hasOne(RoadSignCategoryTranslation::class, 'road_sign_category_id')
            ->when($language, function ($query) use ($language) {
                $query->where('language_id', $language->id);
            });
    }

    public function roadSings(): HasMany
    {
        return $this->hasMany(RoadSign::class, 'category_id');
    }


}
