<?php

namespace App\DTO\Api\V1;

use Spatie\LaravelData\Data;

class ExamTestTranslationDTO extends Data
{
    public function __construct(
        public int $id,
        public int $test_id,
        public int $language_id,
        public string $title,
        public string $created_at,
        public string $updated_at,
        public LanguageDTO $language
    ) {}
}
