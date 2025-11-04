<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PddRule extends Model
{
    protected $fillable = [
        'rule_number',
        'chapter_id',
    ];

    public function translation($lang = null): HasOne
    {
        $language = Language::resolveByCode($lang ?: app()->getLocale());
        $fallback = Language::fallback();

        $relation = $this->hasOne(PddRuleTranslation::class, 'rule_id');

        return Language::applyTranslationScope($relation, $language, $fallback);
    }
}
