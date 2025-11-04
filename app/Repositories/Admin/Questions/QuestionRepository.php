<?php

namespace App\Repositories\Admin\Questions;

use App\DTO\Admin\ListDTO;
use App\Helpers\StorageHelper;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class QuestionRepository implements QuestionRepositoryInterFace
{
    public function getPagination(ListDTO $payload): Collection|LengthAwarePaginator
    {
        $questions =  Question::search($payload->getQuery())
            ->query(function ($query) {
                $query->with(['translation', 'group.translation', 'answers.translation'])
                    ->orderByDesc('id');
            });

            if ($payload->getLimit() === 'all') {
                return $questions->get();
            }

             return $questions->paginate($payload->getLimit());
    }

    public function getById(int $questionId): Question
    {
        return Question::with('translation')->findOrFail($questionId);
    }

    public function store(array $payload): Question
    {
        $fileName = null;
        if (isset($payload['question_image'])) {
            $upload = StorageHelper::uploadFile($payload['question_image'], 'questions');
            $fileName = $upload['file_name'];
        }

        $question = DB::transaction(function () use ($payload, $fileName) {
            // Create the question
            $question = Question::create([
                'image' => $fileName,
                'group_id' => $payload['group_id'],
            ]);

            $question->translation()->create([
                'title' => $payload['question'],
                'language_id' => 102,
            ]);

            // Prepare and create answers
            collect($payload['answers'])->each(function ($answer) use ($payload, $question) {
                $answerModel = Answer::create([
                    'group_id' => $payload['group_id'],
                    'question_id' => $question->id,
                    'is_right' => $answer['is_correct'],
                ]);

                $answerModel->translation()->create([
                    'title' => $answer['option_text'],
                    'language_id' => 102,
                ]);
            });

            return $question;
        });

        return $this->getById($question->id);
    }

    public function update(array $payload, int $questionId): Question
    {
        $fileName = null;

        if (isset($payload['question_image'])) {
            $upload = StorageHelper::uploadFile($payload['question_image'], 'questions');
            $fileName = $upload['file_name'];
        }


        $question = DB::transaction(function () use ($payload, $fileName, $questionId) {
            $question = $this->getById($questionId);

            $question->update([
                'image' => $fileName ?? $question->imageName,
                'group_id' => $payload['group_id'],
            ]);

            $question->translation()->delete();
            $question->translation()->create([
                'title' => $payload['question'],
                'language_id' => 102,
            ]);

            $question->answers()->delete();
            collect($payload['answers'])->each(function ($answer) use ($payload, $question) {
                $answerModel = Answer::create([
                    'group_id' => $payload['group_id'],
                    'question_id' => $question->id,
                    'is_right' => $answer['is_correct'],
                ]);
                $answerModel->translation()->create([
                    'title' => $answer['option_text'],
                    'language_id' => 102,
                ]);
            });

            return $question;
        });

        return $this->getById($question->id);
    }

    public function destroy(int $questionId): void
    {
        $question = $this->getById($questionId);
        StorageHelper::deleteFile($question->image);

        $question->translation()->delete();

        $question->delete();
    }

    public function findByIds(array $questions)
    {
        return Question::query()
            ->whereIn('id', $questions)
            ->with('translation', 'answers.translation')
            ->paginate(200);
    }
}
