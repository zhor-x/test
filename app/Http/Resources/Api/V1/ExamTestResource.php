<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Admin\QuestionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamTestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'duration' => $this->duration,
            'max_wrong_answers' => $this->max_wrong_answers,
            'is_valid' => $this->is_valid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'translation' => new ExamTestTranslationResource($this->translation),
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
            'user_tests' => $this->whenLoaded('userExamTests', function () {
                return $this->userExamTests->map(function ($test) {
                    return [
                        'correct_questions_count' => $test->correct_questions_count,
                        'is_success' => ($this->questions_count - $test->correct_questions_count) <= $test->examTest->max_wrong_answers,
                    ];
                });
            }),
        ];


        return $data;
    }
}
