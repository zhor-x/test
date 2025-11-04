<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'duration' => $this->duration,
            'max_wrong_answers' => $this->max_wrong_answers,
            'hidden' => $this->is_valid,
            'title' => $this->translation->title,
            'questions_count' => $this->when(isset($this->questions_count), $this->questions_count),
            'questions' => $this->whenLoaded('questions', QuestionResource::collection($this->questions)),
        ];
    }
}
