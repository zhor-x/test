<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionsListResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->question_text,
            'image' => $this->question_image ? url('/') . $this->question_image : '',
            'answers' => AnswerResource::collection($this->whenLoaded('options')),
            'category' => $this->category->title
        ];
    }
}
