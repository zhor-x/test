<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'text' => $this->translation?->title,
            'image' => $this->image,
            'group' => $this->group?->translation?->title,
            'category_id' => $this->group_id,
            'exam' => '',
            'answers' => AnswerResource::collection($this->whenLoaded('answers')),
        ];
    }
}
