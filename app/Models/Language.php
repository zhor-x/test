<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['flag', 'country_code', 'country'];

    /**
     * Cache for resolved language records keyed by normalized locale code.
     */
    protected static array $resolvedCache = [];

    /**
     * Resolve a language entry by an incoming locale code.
     *
     * The application receives short locale identifiers ("hy", "ru", "en")
     * while the database stores more specific country based codes
     * ("hy-am", "ru-ru", ...). This helper normalizes the provided value and
     * looks up the appropriate language row by exact match or by prefix.
     */
    public static function resolveByCode(?string $code): ?self
    {
        if (!$code) {
            return null;
        }

        $normalized = strtolower($code);

        if (!array_key_exists($normalized, self::$resolvedCache)) {
            self::$resolvedCache[$normalized] = static::query()
                ->whereRaw('LOWER(country_code) = ?', [$normalized])
                ->orWhere(function ($query) use ($normalized) {
                    $query->whereRaw('LOWER(country_code) LIKE ?', [$normalized . '-%']);
                })
                ->first();
        }

        return self::$resolvedCache[$normalized];
    }

    public function examTestTranslations()
    {
        return $this->hasMany(TestTranslation::class);
    }

    public function questionTranslations()
    {
        return $this->hasMany(QuestionTranslation::class);
    }

    public function answerTranslations()
    {
        return $this->hasMany(AnswerTranslation::class);
    }

    public function explanationTranslations()
    {
        return $this->hasMany(ExplanationTranslation::class);
    }

    public function groupTranslations()
    {
        return $this->hasMany(GroupTranslation::class);
    }
}
