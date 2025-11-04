<?php

namespace App\DTO\Api\V1;

use Spatie\LaravelData\Data;

class LanguageDTO extends Data
{
    public function __construct(
        public int $id,
        public string $flag,
        public string $country_code,
        public string $country,
        public string $created_at,
        public string $updated_at
    ) {}
}
