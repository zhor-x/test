<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoadSignCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->translation->title,
            'description' => $this->translation->description,
            'road_signs' => RoadSignResource::collection($this->whenLoaded('roadSings')),
        ];
    }
}
