<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserExamTestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'exam_id' => $this->test_id,
            'unique_id' => $this->unique_id,
            'is_completed' => $this->is_completed,
            'finish_time' => $this->finish_time,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            'test' => new ExamTestResource($this->examTest),
            'user_exam_test_questions' => UserExamTestQuestionResource::collection($this->questions),
        ];
    }
}
