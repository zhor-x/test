<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'q' => 'sometimes|string'
        ];
    }
}
