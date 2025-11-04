<?php

namespace Database\Seeders;

use App\Helpers\StorageHelper;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

require 'vendor/autoload.php';

class QuestionRuSeeder extends Seeder
{
    const WITHOUT_IMAGE = 'unImage';

    public function run(): void
    {
        // Define configurations for each PDF
        $configs = [
            [
                'pdfPath' => public_path('rus 1.pdf'),
                'unImageIndexes' => [3, 5, 8, 11, 12, 16, 20, 22, 25, 28, 31, 36, 37, 38, 83, 103, 106, 124, 132, 133, 135],
                'category_id' => 1,
            ],
            [
                'pdfPath' => public_path('rus 2.pdf'),
                'unImageIndexes' => [
                    0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 53, 54, 60, 63, 64, 65, 66, 67, 68, 69, 70, 71
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
                'unImageIndexes' => [135],
                'category_id' => 4,
            ],
            [
                'pdfPath' => public_path('rus 5.pdf'),
                'unImageIndexes' => [77, 103, 134],
                'category_id' => 5,
            ],
            [
                'pdfPath' => public_path('rus 6.pdf'),
                'unImageIndexes' => [3, 11, 19, 22, 26, 29, 67],
                'category_id' => 6,
            ],
            [
                'pdfPath' => public_path('rus 7.pdf'),
                'unImageIndexes' => [1, 5, 24, 33, 34, 35, 39, 41, 42, 45, 63, 64, 65, 73, 74, 121],
                'category_id' => 7,
            ],
            [
                'pdfPath' => public_path('rus 8.pdf'),
                'unImageIndexes' => [
                    0, 1, 3, 4, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 17, 18, 19, 20, 21, 22, 23,
                    24, 25, 26, 27, 28, 29,
                    30, 31, 32, 33, 34, 35,
                    36, 37, 38, 39, 40, 41,
                    42, 43, 44, 45, 46, 47,
                    55, 56, 57, 59,
                    60, 64, 65,
                    66, 68, 69, 70, 71,
                    72, 73, 74, 75, 76, 77,
                    78, 79
                ],
                'category_id' => 8,
            ],
            [
                'pdfPath' => public_path('rus 9.pdf'),
                'unImageIndexes' => [
                    0, 1, 3, 4, 5,
                    6, 7, 8, 9, 10, 11,
                    12, 14, 15, 16, 17,
                    18, 23,
                    24, 25, 26, 27, 29,
                    30, 35,
                    36, 37, 38, 39, 40, 41,
                    42, 43, 45, 46, 47,
                    48, 48, 50, 51, 52, 53,
                    54, 55, 56, 57, 58, 59,
                    61, 62, 63, 65,
                    66, 67, 70, 71,
                    73, 75,
                    80,
                    92, 95,
                    99,
                    104,
                    111,
                    114,
                ],
                'category_id' => 9,
            ],
            [
                'pdfPath' => public_path('rus 10.pdf'),
                'unImageIndexes' => self::WITHOUT_IMAGE,
                'category_id' => 10,
            ],
        ];
//

        $parser = new Parser();
        $tempDir = storage_path('app/temp/images');
        $saveDir = storage_path('app/public/questions');

        // Ensure directories exist
        File::ensureDirectoryExists($tempDir);
        File::ensureDirectoryExists($saveDir);

        $pdfimagesAvailable = shell_exec('which pdfimages');
        if (empty($pdfimagesAvailable)) {
            Log::warning("pdfimages not found. Please install poppler-utils.");
            return;
        }

        foreach ($configs as $index => $config) {
            $this->processPdf($config, $parser, $tempDir, $saveDir, $index);
        }

        Log::info("All questions and images seeded successfully.");
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
                $lines = array_filter(array_map('trim', explode("\n", $text)), fn($line) => !empty($line));
                $pageQuestions = $this->parsePageLines($lines);

                $imagePrefix = "{$tempDir}/$configIndex/image-" . ($pageNumber + 1);
                exec("pdfimages -f " . ($pageNumber + 1) . " -l " . ($pageNumber + 1) . " -png " . escapeshellarg($pdfPath) . " " . escapeshellarg($imagePrefix), $output, $returnCode);
                if ($returnCode === 0) {
                    $imageFiles = glob("{$imagePrefix}*.png");
                    sort($imageFiles);
                    Log::info("Found " . count($imageFiles) . " images for page {$pageNumber} in PDF {$pdfPath}");
                } else {
                    Log::warning("pdfimages failed for page " . ($pageNumber + 1) . " in PDF: " . $pdfPath . ", continuing without images");
                }

                $p = 0;
                if ($unImageIndexes !== self::WITHOUT_IMAGE) {
                    foreach ($pageQuestions as &$q) {
                        if (!in_array($globalQuestionIndex - 1, $unImageIndexes) && isset($imageFiles[$p])) {
                            $imageFile = $imageFiles[$p];

                            $upload = StorageHelper::uploadFile($imageFile, 'questionss');
                             $q['image'] = $upload['file_name'];
                            $p++;
                        } else {
                            $q['image'] = null;
                        }
                        $globalQuestionIndex++;
                    }
                }

                 $questions = array_merge($questions, $pageQuestions);
            }

            // Save all questions and options to the database
            foreach ($questions as $payload) {
                $question = new Question([
                    'category_id' => $categoryId,
                    'question_text' => $payload['question'],
                    'question_image' => $payload['image'],
                ]);

                $question->save();

                $answers = collect($payload['answers'])->map(function ($answer, $index) use ($payload) {
                    return [
                        'option_text' => $answer,
                        'is_correct' => $index === $payload['correct_answer'],
                    ];
                });

                $question->options()->createMany($answers->toArray());
            }
        } catch (\Exception $e) {
            Log::error("Seeder error for PDF {$pdfPath}: " . $e->getMessage());
        }
    }

    private function parsePageLines(array $lines): array
    {
        $questions = [];
        $currentQuestion = '';
        $currentAnswers = [];
        $correctAnswer = null;
        $answerIndex = 0;

        foreach ($lines as $line) {
            if (preg_match('/^Պատ․՝([1-5])$/u', $line, $matches)) {
                if ($currentQuestion && !empty($currentAnswers)) {
                    $correctAnswer = (int)$matches[1] - 1;
                    $questions[] = [
                        'question' => trim($currentQuestion),
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

            if (preg_match('/^[1-5]\.\s*(.*)$/u', $line, $matches)) {
                if ($answerIndex > 0 && !empty($currentAnswers[$answerIndex - 1])) {
                    $currentAnswers[$answerIndex - 1] = trim($currentAnswers[$answerIndex - 1]);
                }
                $currentAnswers[$answerIndex] = $matches[1];
                $answerIndex++;
            } elseif ($answerIndex > 0) {
                $currentAnswers[$answerIndex - 1] .= ' ' . $line;
            } else {
                $currentQuestion .= ' ' . $line;
            }
        }

        if ($currentQuestion && !empty($currentAnswers)) {
            $questions[] = [
                'question' => trim($currentQuestion),
                'answers' => array_map('trim', $currentAnswers),
                'correct_answer' => $correctAnswer,
                'image' => null,
            ];
        }

        return $questions;
    }
}
