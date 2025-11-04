<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadSignTranslation extends Model
{

    protected $fillable = [
        'language_id',
        'title',
        'description',
        'road_sign_id',
    ];
}
