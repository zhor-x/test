<?php

namespace App\DTO\Api\V1;

use Spatie\LaravelData\Data;

class ExamTestDTO extends Data
{
    public function __construct(
        public int $id,
        public int $duration,
        public int $max_wrong_answers,
        public bool $is_valid,
        public ?string $deleted_at,
        public string $created_at,
        public string $updated_at,
        public array $translations,
        public ExamTestTranslationDTO $translation
    ) {}
}
