<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExplanationTranslation extends Model
{
    protected $fillable = ['explanation_id', 'language_id', 'title', 'description'];

    public function explanation()
    {
        return $this->belongsTo(Explanation::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
