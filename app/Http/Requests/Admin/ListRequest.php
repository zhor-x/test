<?php

namespace App\Http\Requests\Admin;

use App\DTO\Admin\ListDTO;
use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'limit' => 'required',
            'q' => 'sometimes|nullable|string',
            'order' => 'nullable',
            'type' => 'nullable',
        ];
    }

    public function validated($key = null, $default = null): ListDTO
    {
        $validatedPayload = parent::validated($key, $default);

        return new ListDTO(
            $validatedPayload['limit'] ?? 10,
            $validatedPayload['q'] ?? null,
                $validatedPayload['type'] ?? null,
                $validatedPayload['order'] ?? null,
        );
    }
}
