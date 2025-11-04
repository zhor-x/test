<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticQuestionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->question->id,
            'group'=> $this->question->group->id,
            'image' => $this->question->image,
            'group_id' => $this->question->group_id,
            'translation' => new GroupQuestionTranslationResource($this->question->translation),
            'answers' => GroupAnswerResource::collection($this->question->answers),
            'user_answer'=> $this->answer_id,
            'is_right' => $this->is_right
        ];
    }
}
