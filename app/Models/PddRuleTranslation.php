<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PddRuleTranslation extends Model
{
    protected $fillable = [
        'rule_id',
        'language_id',
        'content',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(PddRule::class, 'rule_id');
    }

}
