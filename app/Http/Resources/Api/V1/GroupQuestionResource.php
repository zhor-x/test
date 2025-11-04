<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupQuestionResource extends JsonResource
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
            'image' => $this->image,
            'group_id' => $this->group_id,
            'group_number' => $this->group_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'translation' => new GroupQuestionTranslationResource($this->translation),
            'answers' => GroupAnswerResource::collection($this->answers),
        ];
    }
}
