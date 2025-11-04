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

        return $this->hasOne(PddRuleTranslation::class, 'rule_id')
            ->when($language, function ($query) use ($language) {
                $query->where('language_id', $language->id);
            });
    }
}
