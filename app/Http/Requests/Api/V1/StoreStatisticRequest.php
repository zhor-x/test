<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreStatisticRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'question_id' => 'required|integer',
            'answer_id' => 'required|integer',
            'is_right' => 'required|boolean',
            'group_id' => 'required|integer',
        ];
    }
}
