<?php

namespace App\Console\Commands;

use App\Models\RoadSign;
use App\Models\RoadSignCategory;
use Illuminate\Console\Command;

class AddRoadSignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-road-sign-command';

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
        RoadSign::query()->delete();
        $filePath = storage_path('app/public/traffic_signs.json');

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

        $category = RoadSignCategory::query()->get();
        foreach ($data as $key => $items) {

            foreach ($items as $item) {

                  if ($item['image']!=null) {

                    $road = new RoadSign;
                    $road->image = $item['image'];
                    $road->category_id = $category[$key]->id;
                    $road->save(); //$road

                    $road->translation()->create([
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'language_id' => 102
                    ]);
                }
            }
        }
        $this->info("Command completed successfully!");
        return 0;
    }
}
