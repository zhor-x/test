<?php

namespace App\Console\Commands;

use App\Helpers\StorageHelper;
use App\Models\Question;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\DB;

class GTHCommand extends Command
{
    protected $signature = 'app:g-t-h-command';
    protected $description = 'Process PDF and store questions in the database';

    public function handle()
    {
        $pdf = public_path('daltonism.pdf');
        $parser = new Parser();
        $tempDir = storage_path('app/temp/images');
        $saveDir = storage_path('app/public/gth');

        File::ensureDirectoryExists($tempDir);
        File::ensureDirectoryExists($saveDir);

        $pdfimagesAvailable = shell_exec('which pdfimages');
        if (empty($pdfimagesAvailable)) {
            Log::warning("pdfimages not found. Please install poppler-utils.");
            return 1;
        }

        try {
            DB::beginTransaction();
            $this->processPdf($pdf, $parser, $tempDir, $saveDir);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Seeder error for PDF {$pdf}: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function processPdf($pdf, Parser $parser, string $tempDir, string $saveDir): void
    {
        $pdfPath = $pdf;
        $categoryId = 63;

        $pdf = $parser->parseFile($pdfPath);
        $pages = $pdf->getPages();
        $questions = [];

        foreach ($pages as $pageNumber => $page) {
            $text = $page->getText();
            $lines = array_filter(array_map('trim', explode("\n", $text)), fn($line) => !empty($line));
            $pageQuestions = $this->parsePageLines($lines);

            $imagePrefix = "{$tempDir}/image-" . ($pageNumber + 1);
            exec("pdfimages -f " . ($pageNumber + 1) . " -l " . ($pageNumber + 1) . " -png " . escapeshellarg($pdfPath) . " " . escapeshellarg($imagePrefix), $output, $returnCode);

            if ($returnCode === 0) {
                $imageFiles = glob("{$imagePrefix}*.png");
                sort($imageFiles);
                Log::info("Found " . count($imageFiles) . " images for page {$pageNumber} in PDF {$pdfPath}");
            } else {
                Log::warning("pdfimages failed for page " . ($pageNumber + 1) . " in PDF: " . $pdfPath . ", continuing without images");
                $imageFiles = [];
            }

            $p = 0;
            foreach ($pageQuestions as &$q) {
                if (isset($imageFiles[$p])) {
                    $upload = StorageHelper::uploadFile($imageFiles[$p], 'gth');
                    $q['image'] = $upload['file_name'];
                } else {
                    $q['image'] = null;
                }
                $p++;
            }

            $questions = array_merge($questions, $pageQuestions);
        }

        foreach ($questions as $payload) {
            $question = new Question([
                'category_id' => $categoryId,
                'question_text' => $payload['gth'],
                'group_id' => 11,
                'image' => $payload['image'],
            ]);

            $question->save();

            $question->translation()->create([
                'title' => $payload['gth'],
                'language_id' => 102,
            ]);

            collect($payload['answers'])->map(function ($answer, $index) use ($payload, $question) {
                $answerData = [
                    'option_text' => $answer,
                    'is_right' => $index === $payload['correct_answer'],
                    'group_id' => 11,
                ];

                $answer = $question->answers()->create($answerData);

                $answer->translation()->create([
                    'title' => $answer->option_text,
                    'language_id' => 102,
                ]);

                return $answerData;
            });
        }
    }

    private function parsePageLines(array $lines): array
    {
        $questions = [];
        $currentQuestion = null;
        $currentAnswers = [];
        $correctIndex = null;
        $parsingAnswers = false;

        foreach ($lines as $line) {
            $line = trim($line);

            if (preg_match('/^\d+\.(.*?)[:։\?]+$/u', $line, $matches)) {
                if ($currentQuestion && count($currentAnswers) > 0) {
                    $questions[] = [
                        'gth' => trim($currentQuestion),
                        'answers' => array_map('trim', $currentAnswers),
                        'correct_answer' => $correctIndex,
                        'image' => null,
                    ];
                }

                $currentQuestion = trim($matches[1]);
                $currentAnswers = [];
                $correctIndex = null;
                $parsingAnswers = true;
            } elseif ($parsingAnswers && preg_match('/^\d+\.(.*)/u', $line, $answerMatches)) {
                $answer = trim($answerMatches[1]);

                if (str_contains($answer, '*')) {
                    $answer = str_replace('*', '', $answer);
                    $correctIndex = count($currentAnswers);
                }

                $currentAnswers[] = $answer;
            } else {
                $parsingAnswers = false;
            }
        }

        if ($currentQuestion && count($currentAnswers) > 0) {
            $questions[] = [
                'gth' => trim($currentQuestion),
                'answers' => array_map('trim', $currentAnswers),
                'correct_answer' => $correctIndex,
                'image' => null,
            ];
        }

        return $questions;
    }
}
