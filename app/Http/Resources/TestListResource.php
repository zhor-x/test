<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class TestListResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $user = Auth::user();
        $userExam = $this->userExams->where('user_id', $user->id)->first();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'total_questions' => $this->questions->count(),
            'user_progress' => $userExam ? [
                'total_answered' => $userExam->answers->count(),
                'correct_answers' => $userExam->answers->where('is_correct', true)->count(),
            ] : null,
        ];
    }
}
