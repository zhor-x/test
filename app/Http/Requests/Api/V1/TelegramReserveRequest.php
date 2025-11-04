<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class TelegramReserveRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'=> ['string', 'required'],
            'phone' => ['string', 'required'],
            'tariff' => ['string', 'required']
        ];
    }
}
