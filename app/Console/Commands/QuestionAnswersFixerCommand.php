<?php

namespace App\Console\Commands;

use App\Models\AnswerTranslation;
use App\Models\QuestionTranslation;
use Illuminate\Console\Command;

class QuestionAnswersFixerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix';

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
        $translations = QuestionTranslation::all();

        foreach ($translations as $translation) {
            // Get the title and clean up the text
//            $cleanedTitle = preg_replace('/\s+/', ' ', $translation->title); // Replace multiple spaces with a single space
            $cleanedTitle = str_replace('եւ', 'և', $translation->title); // Replace 'եւ' with 'և'
//            $cleanedTitle = str_replace('`', '՝', $cleanedTitle); // Replace apostrophe (`) with Armenian quotation mark (՝)

            // Update the title in the database
            $translation->title = $cleanedTitle;
            $translation->save();
        }

       $answers =  AnswerTranslation::all();

        foreach ($answers as $translation) {
            // Get the title and clean up the text
//            $cleanedTitle = preg_replace('/\s+/', ' ', $translation->title); // Replace multiple spaces with a single space
            $cleanedTitle = str_replace('եւ', 'և', $translation->title); // Replace 'եւ' with 'և'
//            $cleanedTitle = str_replace('`', '՝', $cleanedTitle); // Replace apostrophe (`) with Armenian quotation mark (՝)

            // Update the title in the database
            $translation->title = $cleanedTitle;
            $translation->save();
        }

        $this->info('Titles cleaned successfully!');
    }
}
