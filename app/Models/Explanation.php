<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Explanation extends Model
{
    protected $fillable = ['group_id', 'question_id'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ExplanationTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(ExplanationTranslation::class)->where('language_id', 201);
    }
}
