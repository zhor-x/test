<?php

namespace App\Models;

use App\Helpers\StorageHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class Question extends Model
{
     use Searchable;

    protected $fillable = ['image', 'group_id'];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function translations()
    {
        return $this->hasMany(QuestionTranslation::class);
    }

    public function translation($lang = null)
    {
        $language = Language::resolveByCode($lang ?: app()->getLocale());

        return $this->hasOne(QuestionTranslation::class, 'question_id', 'id')
            ->when($language, fn($q) => $q->where('language_id', $language->id));
    }

    public function translationCommand()
    {
        return $this->hasOne(QuestionTranslation::class, 'question_id', 'id');
    }

    public function answers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Answer::class, 'question_id');
    }

    public function explanation()
    {
        return $this->hasOne(Explanation::class, 'question_id');
    }

    public function examTests(): BelongsToMany
    {
        return $this->belongsToMany(Test::class, 'test_questions', 'question_id', 'test_id')
            ->withPivot('id')
            ->withTimestamps();
    }

    public function image(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? url('/') . StorageHelper::getFileUrl('questions/' . $value) : '',
        );
    }

    public function imageName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->attributes['image'] ?? '',
        );
    }



    public function toSearchableArray(): array
    {
        $this->loadMissing('translation');

          return [
            'id' => (string)$this->id,
            'title' =>  $this->translation->title??'',
        ];
    }
}
