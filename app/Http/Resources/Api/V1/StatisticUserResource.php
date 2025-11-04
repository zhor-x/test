<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticUserResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'total_count' => $this->total_count,
            'incorrect_count' => $this->incorrect_count,
        ];
    }
}
