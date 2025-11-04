<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadSignCategoryTranslation extends Model
{

    protected $table = 'road_sign_category_translations';

    protected $fillable = [
        'title',
        'description',
        'language_id',
        'road_sign_category_id',
    ];
}
