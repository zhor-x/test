<?php

namespace App\Models;

use App\Helpers\StorageHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;

class RoadSign extends Model
{
    use Searchable;

    public function category()
    {
        return $this->belongsTo(RoadSignCategory::class);
    }


    public function translation($lang = null): HasOne
    {
        $language = Language::resolveByCode($lang ?: app()->getLocale());
        $fallback = Language::fallback();

        $relation = $this->hasOne(RoadSignTranslation::class, 'road_sign_id');

        return Language::applyTranslationScope($relation, $language, $fallback);
    }


    public function image(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? url('/') . StorageHelper::getFileUrl('road-signs/' . $value) : '',
        );
    }
    public function toSearchableArray(): array
    {
        return array_merge($this->toArray(), [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'title' => $this->title,
            'category_title' => $this->category->title,
        ]);
    }

}
