<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestTranslation extends Model
{
    protected $fillable = ['test_id', 'language_id', 'title'];

    public function examTest()
    {
        return $this->belongsTo(Test::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
