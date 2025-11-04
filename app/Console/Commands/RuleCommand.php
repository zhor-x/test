<?php

namespace App\Console\Commands;

use App\Models\PddChapters;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rule-command';

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
//        PddChapters::query()->delete();
        DB::transaction(function () {
            $filePath = storage_path('app/public/road_rules_en.json');
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

            foreach ($data as $key => $items) {
                $chapter = new PddChapters;
                $chapter->save();
                $chapter->translation()->create([
                    'title' => $items['title'],
                    'language_id' => 100
                ]);

                foreach ($items['subsections'] as $item) {
                    $rules = $chapter->rules()->create([
                        'rule_number' => $key,
                    ]);
                    $rules->translation()->create([
                        'content' => trim($item['text']),
                        'language_id' => 100
                    ]);
                }
            }

            return  1;
        });
    }
}
