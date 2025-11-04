<?php

namespace App\Repositories\Admin\Tests;

use App\DTO\Admin\ListDTO;
use App\Models\Test;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminTestRepository implements AdminTestRepositoryInterFace
{
    public function getPagination(ListDTO $listDTO): LengthAwarePaginator
    {
        $test = Test::with(['translation'])
            ->withCount('questions');
        if ($listDTO->getOrderBy()) {
          $test =   $test->orderBy($listDTO->getOrderBy(), $listDTO->getOrder());
        }

       return $test->paginate();
    }

    public function getById(int $questionId): Test
    {
        return Test::with(['translation', 'questions'])->findOrFail($questionId);
    }

    public function store(array $payload): Test
    {
        $test = DB::transaction(function () use ($payload) {

            $test = Test::create([
                'duration' => $payload['duration'],
                'max_wrong_answers' => $payload['max_wrong_answers'],
                'is_valid' => !$payload['hidden'],
            ]);

            $test->translation()->create([
                'title' => $payload['title'],
                'language_id' => 102,
            ]);

            foreach ($payload['questions']as $question) {
                $test->questions()->attach($question);
            }

            return $test;

        });
        return $this->getById($test->id);
    }

    public function update(array $payload, int $testId): Test
    {
        $test = DB::transaction(function () use ($payload, $testId) {
            $test = $this->getById($testId);

            $test->update([
                'duration' => $payload['duration'],
                'max_wrong_answers' => $payload['max_wrong_answers'],
                'is_valid' => !$payload['hidden'],
            ]);

            $test->translation()->update([
                'title' => $payload['title'],
                'language_id' => 102,
            ]);


            if (isset($payload['questions']) && count($payload['questions']) > 0) {
                $test->questions()->detach();

            }
            foreach ($payload['questions']as $question) {
                $test->questions()->attach($question);
            }

            return $test;

        });
        return $this->getById($test->id);
    }

    public function destroy(int $questionId): void
    {
        $question = $this->getById($questionId);
        $question->translation()->delete();

        $question->delete();
    }
}
