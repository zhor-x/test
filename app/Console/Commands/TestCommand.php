<?php

namespace App\Console\Commands;

use App\Models\AnswerTranslation;
use App\Models\Question;
use App\Models\QuestionTranslation;
use App\Models\TestQuestion;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Delete all existing ExamTestQuestion records
        TestQuestion::query()->delete();
        $filePath = storage_path('app/questions.json');
        if (!file_exists($filePath)) {
            $this->error("JSON file not found at: $filePath");
            return 1;
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON: ' . json_last_error_msg());
            return 1;
        }

        $questions = Question::with(['translation', 'answers.translation'])->get();
        foreach ($data as $item) {
            $testId = $item['test_id'];
            $sanitizedQuestion = $this->getSanitizedTitle($item['question']);
            $sanitizedJsonAnswers = array_map([$this, 'getSanitizedTitle'], $item['answers']);
            sort($sanitizedJsonAnswers);

            $matchingQuestion = $questions->first(function ($q) use ($sanitizedQuestion, $sanitizedJsonAnswers) {
                $sanitizedDbTitle = $this->getSanitizedTitle($q->translation->title);
                $titleSimilarity = $this->getSimilarityPercentage($sanitizedDbTitle, $sanitizedQuestion);

                if ($titleSimilarity < 90) {
                    return false;
                }

                $sanitizedDbAnswers = $q->answers->map(function ($answer) {
                    return $this->getSanitizedTitle($answer->translation->title);
                })->sort()->values()->toArray();

                $answerSimilarity = $this->getAnswerSimilarity($sanitizedJsonAnswers, $sanitizedDbAnswers);

                return $answerSimilarity > 80;
            });

            if ($matchingQuestion) {
                $currentCount = TestQuestion::where('test_id', $testId)->count();
                if ($currentCount < 20) {
                    TestQuestion::firstOrCreate([
                        'test_id' => $testId,
                        'question_id' => $matchingQuestion->id
                    ]);
                    $this->info("Processed question for test $testId: $sanitizedQuestion");
                } else {
                    $this->warn("Test $testId has reached the limit of 20 questions");
                }
            } else {
                $this->warn("No matching question found for: $sanitizedQuestion");
            }
        }

        $this->info("Command completed successfully!");
        return 0;
    }

    private function getSanitizedTitle($text)
    {
        // Normalize the text by converting it to lowercase
        $text = mb_strtolower($text ?? '', 'UTF-8');

        // Normalize special characters like the Armenian question mark
        $text = preg_replace('/[^\p{L}\p{N}\s\?]/u', '', $text); // Allow spaces and ? in the sanitized text

        // Normalize spaces
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }

    private function getSimilarityPercentage($text1, $text2)
    {
        similar_text($text1, $text2, $percent);
        return $percent;
    }

    private function getAnswerSimilarity($answers1, $answers2)
    {
        similar_text(join('|', $answers1), join('|', $answers2), $percent);
        return $percent;
    }
}
