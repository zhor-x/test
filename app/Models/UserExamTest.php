<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class UserExamTest extends Model
{
    protected $fillable = ['user_id', 'test_id', 'unique_id', 'is_completed', 'finish_time'];



    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->unique_id)) {
                $model->unique_id = (string)Str::uuid();
            }
        });
    }

    public function examTest(): BelongsTo
    {
        return $this->belongsTo(Test::class, 'test_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(UserExamTestQuestion::class, 'user_exam_test_id');
    }
}
