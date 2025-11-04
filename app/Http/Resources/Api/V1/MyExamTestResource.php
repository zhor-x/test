<?php

namespace App\Http\Resources\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyExamTestResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            "unique_id" => $this->unique_id,
            "is_completed" => $this->is_completed,
            "finish_time" => $this->finish_time,
            "created_at" => Carbon::parse($this->created_at)->format('d-m-Y H:i'),
            'test' => new ExamTestResource($this->examTest),
            'user_tests' => [
                'questions_count' => $this->questions_count,
                'correct_questions_count' => $this->correct_questions_count,
                'is_success' => ($this->questions_count - $this->correct_questions_count) <= $this->examTest->max_wrong_answers
            ]
        ];
    }
}
