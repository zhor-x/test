<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserExamTestQuestionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_test_id' => $this->user_exam_test_id,
            'exam_test_question_id' => $this->question->question_id,
            'exam_test_answer_id' => $this->test_answer_id,
            'is_right' => $this->is_right,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
