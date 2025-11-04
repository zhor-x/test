<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;


class StatisticCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $groupedByQuestion = $this->collection
            ->groupBy('question_id')
            ->map(function ($items) {
                $first = $items->first();
                $question = $first->question;
                $group = $question->group;

                return [
                    'question_id' => $question->id,
                    'question_title' => $question->translation?->title,
                    'wrong_count' => $items->count(),
                    'group_id' => $group->id,
                    'group_title' => $group->translation?->title,
                    'group_description' => $group->translation?->description,
                ];
            });

        return $groupedByQuestion
            ->groupBy('group_id')
            ->map(function ($questions) {
                $first = $questions->first();

                return [
                    'group' => [
                        'id' => $first['group_id'],
                        'title' => $first['group_title'],
                        'description' => $first['group_description'],
                        'questions' => $questions->map(fn($q) => [
                            'id' => $q['question_id'],
                            'title' => $q['question_title'],
                            'wrong_count' => $q['wrong_count'],
                        ])->values(),
                    ],
                ];
            })
            ->values();
    }
}

