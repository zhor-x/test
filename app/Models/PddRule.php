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

    public function translation($lang = null): ?HasOne
    {
        $lang = $lang ?: app()->getLocale();
        $language = Language::query()->where('country_code', $lang)->first();

        if (!$language) {
            return null;
        }
         return $this->hasOne(PddRuleTranslation::class, 'rule_id')->where('language_id', $language->id);
    }
}
