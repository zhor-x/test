<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    public function userExams(): HasMany
    {
        return $this->hasMany(UserExam::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
