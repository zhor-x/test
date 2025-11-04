<?php

namespace App\Repositories\Api\V1;

use App\Helpers\TransliteratorHelper;
use App\Models\User;
use App\Models\UserStatistic;
use Illuminate\Pagination\LengthAwarePaginator;

class StatisticRepository implements StatisticRepositoryInterface
{

    public function getUserWrongList()
    {
        $userId = auth('api')->user()->id;

        $statistics = UserStatistic::query()->select('question_id', 'id')
            ->with([
                'question' => function ($q) {
                    $q->select('id', 'group_id', 'image')
                        ->with([
                            'translation' => fn($qt) => $qt->select('id', 'question_id', 'title')->where('language_id', 102),
                            'answers' => fn($a) => $a->select('id', 'question_id', 'is_right')->with([
                                'translation' => fn($at) => $at->select('id', 'answer_id', 'title')->where('language_id', 102)
                            ])
                        ]);
                }
            ])
            ->where('user_id', $userId)
            ->where('is_right', 0)
            ->get();


        return $statistics;

    }

    public function submit(array $payload): void
    {
        $userSurvey = new UserStatistic;
        $userSurvey->user_id = auth('api')->user()->id;
        $userSurvey->question_id = $payload['question_id'];
        $userSurvey->answer_id = $payload['answer_id'];
        $userSurvey->is_right = $payload['is_right'];
        $userSurvey->save();
    }

    public function updateStatistic(int $id, bool $isCorrect): void
    {
        UserStatistic::find($id)->update(['is_right' => $isCorrect]);
    }

    public function getGroupUserList($payload): LengthAwarePaginator
    {

        $term = TransliteratorHelper::transliterate($payload['q'] ?? '');
        $userSearch = User::search($term);

        $users = $userSearch->get();

        $userIds = $users->pluck('id')->toArray();

        $query = UserStatistic::query()
            ->select('user_id')
            ->selectRaw('MAX(id) as id')
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('COUNT(CASE WHEN is_right = 0 THEN 1 END) as incorrect_count')
            ->whereIn('user_id', $userIds)
            ->with('user')
            ->groupBy('user_id');

        return $query->paginate(20);
    }

    public function getUserCategoryList(int $userId): LengthAwarePaginator
    {
        return UserStatistic::query()
            ->selectRaw('MAX(id) as id, user_id, DATE(created_at) as created_date')
            ->where('user_id', $userId)
            ->groupBy('user_id', 'created_date')
            ->orderByDesc('id')
            ->paginate(20);
    }

    public function getByDate(int $userId, string $date)
    {
        return UserStatistic::query()
            ->where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->with([
                'question.translation',
                'question.group.translation',
                'question.answers.translation',
                'question'])
             ->get()
            ->groupBy(fn($item) => optional($item->question->group)->id);


    }
}
