<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Str;

class CompareQuestionTranslations extends Command
{
    protected $signature = 'questions:compare';
    protected $description = 'Сравнить армянские и русские вопросы и найти отсутствующие переводы';

    public function handle(): void
    {
        $parser = new Parser();

        $armText = $parser->parseFile(storage_path('app/questions/arm.pdf'))->getText();
        $rusText = $parser->parseFile(storage_path('app/questions/rus.pdf'))->getText();

        // Извлекаем вопросы по шаблону
        $armQuestions = $this->extractQuestions($armText);
        $rusQuestions = $this->extractQuestions($rusText);

        $this->info('Всего армянских вопросов: ' . count($armQuestions));
        $this->info('Всего русских вопросов: ' . count($rusQuestions));

        // Сравнение по содержанию
        $missing = collect($armQuestions)->reject(function ($question) use ($rusQuestions) {
            foreach ($rusQuestions as $rus) {
                if (similar_text($question, $rus, $percent) && $percent > 85) {
                    return true;
                }
            }
            return false;
        });

        $this->warn('Отсутствующие вопросы (' . $missing->count() . '):');
        foreach ($missing as $q) {
            $this->line('- ' . Str::limit($q, 120));
        }
    }

    private function extractQuestions(string $text): array
    {
        // Простая очистка
        $text = preg_replace('/\s+/', ' ', $text);
        // Разделяем по признаку вопроса — "?" или "Ո՞ր" / "Который"
        preg_match_all('/([^.?!]+[?։\?])/', $text, $matches);

        // Убираем варианты ответов и пробелы
        $questions = array_map(fn($q) => trim(strip_tags($q)), $matches[1]);

        return array_filter($questions, fn($q) => mb_strlen($q) > 15);
    }
}
