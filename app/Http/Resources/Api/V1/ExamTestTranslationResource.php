<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamTestTranslationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'test_id' => $this->test_id,
            'language_id' => $this->language_id,
            'title' => $this->title,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'language' => new LanguageResource($this->language),
        ];
    }
}
