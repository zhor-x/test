<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PddChapterTranslation extends Model
{
    protected $fillable = [
        'chapter_id',
        'language_id',
        'title'
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(PddChapters::class, 'chapter_id');
    }
}
