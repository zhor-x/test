<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Admin\AnswerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'test_name' => $this->examTest->translation->title,
            'questions' => $this->whenLoaded('examTest', function () {
                return $this->examTest->questions->map(function ($question) {
                    return [
                        'id' => $question->pivot->id,
                        'question_id' => $question->id,
                        'text' => $question->translation->title,
                        'image' => $question->image,
                        'answers' => AnswerResource::collection($question->answers),
                    ];
                });
            }),
            'user_test' => $this->whenLoaded('questions', function () {
                return $this->questions->map(function ($test) {
                    return [
                        'test_answer_id' => $test->test_answer_id,
                        'test_question_id' => $test->test_question_id,
                        'is_right' => $test->is_right,
                    ];
                });
            }),
        ];
    }
}
