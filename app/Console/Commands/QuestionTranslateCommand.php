<?php

namespace App\Console\Commands;

use App\Helpers\StorageHelper;
use App\Models\Answer;
use App\Models\AnswerTranslation;
use App\Models\Question;
use App\Models\QuestionTranslation;
use App\Models\Test;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class QuestionTranslateCommand extends Command
{
    const WITHOUT_IMAGE = 'unImage';

    protected $signature = 'question-translate-command';
    protected $description = 'Добавляет русские переводы для вопросов, у которых уже есть армянская версия, по фото/тексту.';

    public function handle()
    {

        $configs = [
            [
                'pdfPath' => public_path('rus 1.pdf'),
                'unImageIndexes' => [3, 5, 8, 11, 12, 16, 20, 22, 25, 28, 31, 36, 37, 38, 78, 92, 95, 108, 116, 117, 119],
                'category_id' => 1,
            ],
            [
                'pdfPath' => public_path('rus 2.pdf'),
                'unImageIndexes' => [
                    0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 52, 53, 59, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71
                ],
                'category_id' => 2,
            ],
            [
                'pdfPath' => public_path('rus 3.pdf'),
                'unImageIndexes' => self::WITHOUT_IMAGE,
                'category_id' => 3,
            ],
            [
                'pdfPath' => public_path('rus 4.pdf'),
                'unImageIndexes' => [113],
                'category_id' => 4,
            ],
            [
                'pdfPath' => public_path('rus 5.pdf'),
                'unImageIndexes' => [75, 101, 129],
                'category_id' => 5,
            ],
            [
                'pdfPath' => public_path('rus 6.pdf'),
                'unImageIndexes' => [3, 12, 20, 23, 27, 30, 67],
                'category_id' => 6,
            ],
            [
                'pdfPath' => public_path('rus 7.pdf'),
                'unImageIndexes' => [1, 5, 24, 33, 34, 35, 39, 41, 42, 45, 63, 64, 65, 73, 74, 123],
                'category_id' => 7,
            ],
            [
                'pdfPath' => public_path('rus 8.pdf'),
                'unImageIndexes' => [
                    0, 1, 3, 4,
                    6, 7, 8, 9, 10, 11,
                    12, 13, 14, 15, 17,
                    18, 19, 20, 21, 22, 23,
                    24, 25, 26, 27, 28, 29,
                    30, 31, 32, 33, 34, 35,
                    36, 37, 38, 39, 40, 41,
                    42, 43, 44, 45, 46, 47,
                    53,
                    54, 55, 57, 68,
                    62, 63, 64, 65,
                    66,67, 68, 69, 70, 71,
                    72, 73, 74, 75, 76
                ],
                'category_id' => 8,
            ],
            [
                'pdfPath' => public_path('rus 9.pdf'),
                'unImageIndexes' => [
                    0, 1, 3, 4, 5,
                    6, 7, 8, 9, 10, 11,
                    12, 14, 15, 16,
                    18, 23,
                    24, 25, 26, 27, 29,
                    30, 35,
                    36, 37, 38, 39, 40, 41,
                    42, 43, 45, 46, 47,
                    48, 49, 50, 51, 52, 53,
                    54, 55, 56, 57, 58, 59,
                    61, 62, 63, 65,
                    66, 67, 70, 71,
                    73, 75,
                    80,
                    91, 94,
                    98,
                    102,
                    109,
                    112,
                ],
                'category_id' => 9,
            ],
            [
                'pdfPath' => public_path('rus 10.pdf'),
                'unImageIndexes' => self::WITHOUT_IMAGE,
                'category_id' => 10,
            ],
        ];

        QuestionTranslation::query()->where('language_id', 101)->delete();
        AnswerTranslation::query()->where('language_id', 101)->delete();

        $parser = new Parser();
        $tempDir = storage_path('app/temp/images');
        $saveDir = storage_path('app/public/questions');

        File::ensureDirectoryExists($tempDir);
        File::ensureDirectoryExists($saveDir);

        if (empty(shell_exec('which pdfimages'))) {
            Log::warning("pdfimages not found. Please install poppler-utils.");
            return;
        }

        foreach ($configs as $index => $config) {
            $this->processPdf($config, $parser, $tempDir, $saveDir, $index);
        }

        // После импорта всех вопросов — создаём тесты
        $this->generateTests();

        Log::info("✅ Все вопросы, ответы и тесты успешно добавлены.");
    }

    private function processPdf(array $config, Parser $parser, string $tempDir, string $saveDir, $configIndex): void
    {
        $pdfPath = $config['pdfPath'];
        $unImageIndexes = $config['unImageIndexes'];
        $categoryId = $config['category_id'];

        try {
            $pdf = $parser->parseFile($pdfPath);
            $pages = $pdf->getPages();
            $questions = [];
            $globalQuestionIndex = 1;

            foreach ($pages as $pageNumber => $page) {
                $text = $page->getText();
                $lines = array_filter(array_map('trim', explode("\n", $text)));
                $pageQuestions = $this->parsePageLines($lines, $pageNumber);

                $imagePrefix = "{$tempDir}/$configIndex/image-" . ($pageNumber + 1);
                exec("pdfimages -f " . ($pageNumber + 1) . " -l " . ($pageNumber + 1) . " -png " . escapeshellarg($pdfPath) . " " . escapeshellarg($imagePrefix));
                $imageFiles = glob("{$imagePrefix}*.png") ?: [];
                sort($imageFiles);

                $p = 0;
                foreach ($pageQuestions as &$q) {
                    $hasImage = $unImageIndexes !== self::WITHOUT_IMAGE && !in_array($globalQuestionIndex - 1, $unImageIndexes);
                    if ($hasImage && isset($imageFiles[$p])) {
                        $upload = StorageHelper::uploadFile($imageFiles[$p], 'questions');
                        $q['image'] = $upload['file_name'];
                        $p++;
                    } else {
                        $q['image'] = null;
                    }
                    $globalQuestionIndex++;
                }
                unset($q);

                $questions = array_merge($questions, $pageQuestions);
            }

            foreach ($questions as $payload) {
                $question = Question::create([
                    'image' => $payload['image'],
                    'group_id' => $categoryId,
                ]);

                QuestionTranslation::create([
                    'question_id' => $question->id,
                    'language_id' => 101,
                    'title' => $payload['question'],
                ]);

                foreach ($payload['answers'] as $index => $answerText) {
                    $answer = Answer::create([
                        'is_right' => $index === $payload['correct_answer'],
                        'group_id' => $categoryId,
                        'question_id' => $question->id,
                    ]);

                    AnswerTranslation::create([
                        'answer_id' => $answer->id,
                        'language_id' => 101,
                        'title' => $answerText,
                    ]);
                }
            }

            Log::info("✅ Обработан PDF: {$pdfPath}");
        } catch (\Exception $e) {
            Log::error("Ошибка при обработке PDF {$pdfPath}: " . $e->getMessage());
        }
    }

    private function parsePageLines(array $lines, int $pageNumber): array
    {
        $questions = [];
        $currentQuestion = '';
        $currentAnswers = [];
        $correctAnswer = null;
        $answerIndex = 0;

        foreach ($lines as $line) {
            if (preg_match('/^отв․՝([1-9])$/u', $line, $matches)) {
                if (!empty($currentAnswers)) {
                    $correctAnswer = (int)$matches[1] - 1;
                    $questions[] = [
                        'question' => $currentQuestion ?: 'NoQuestion',
                        'answers' => array_map('trim', $currentAnswers),
                        'correct_answer' => $correctAnswer,
                        'image' => null,
                    ];
                    $currentQuestion = '';
                    $currentAnswers = [];
                    $answerIndex = 0;
                }
                continue;
            }

            if (preg_match('/^[1-9]\.\s*(.*)$/u', $line, $matches)) {
                $currentAnswers[$answerIndex++] = $matches[1];
            } elseif ($answerIndex > 0) {
                $currentAnswers[$answerIndex - 1] .= ' ' . $line;
            } else {
                $currentQuestion .= ' ' . $line;
            }
        }

        if (!empty($currentAnswers)) {
            $questions[] = [
                'question' => trim($currentQuestion) ?: 'NoQuestion',
                'answers' => array_map('trim', $currentAnswers),
                'correct_answer' => $correctAnswer,
                'image' => null,
            ];
        }

        return $questions;
    }

    private function generateTests(): void
    {
        $allQuestions = Question::query()
            ->whereHas('translationCommand', fn($q) => $q->where('language_id', 101))
            ->get()
            ->groupBy('group_id');

        if ($allQuestions->isEmpty()) {
            Log::warning("❌ Нет доступных вопросов для создания тестов.");
            $this->error("Нет доступных вопросов для создания тестов.");
            return;
        }

        Log::info("Создание 62 тестов...");

        foreach (range(1, 62) as $id) {
            $test = Test::create([
                'duration' => 30,
                'max_wrong_answers' => 2,
                'is_valid' => true,
            ]);

            $test->translation()->firstOrCreate(
                ['language_id' => 101],
                ['title' => "Тест $id"]
            );

            // Берём случайную категорию
            $categoryId = $allQuestions->keys()->random();
            Log::info($categoryId);
            $categoryQuestions = $allQuestions[$categoryId];

            if ($categoryQuestions->isEmpty()) {
                Log::warning("Категория {$categoryId} пуста — пропускаем тест {$id}.");
                continue;
            }

            $randomQuestions = $categoryQuestions->random(min(30, $categoryQuestions->count()));

            $test->questions()->syncWithoutDetaching($randomQuestions->pluck('id')->toArray());
        }

        Log::info("✅ Тесты успешно созданы и заполнены вопросами.");
    }
}
