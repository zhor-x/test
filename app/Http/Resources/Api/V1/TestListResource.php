<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestListResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'total_questions' => $this->questions_count,
            'user_progress' => $this->whenLoaded('latestUserAnswerStat', function () {
                return $this->latestUserAnswerStat->correct_count;
            }),
        ];
    }
}
