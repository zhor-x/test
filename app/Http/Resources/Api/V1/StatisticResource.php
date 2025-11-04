<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Admin\QuestionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'question' => new QuestionResource($this->whenLoaded('question')),
        ];
    }
}
