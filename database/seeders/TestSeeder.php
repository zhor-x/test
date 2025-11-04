<?php

namespace Database\Seeders;

use App\Models\Test;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 62) as $id) {
          $test =   Test::firstOrCreate(
                ['id' => $id],
                [
                    'duration' => 30,
                    'max_wrong_answers' => 2,
                    'is_valid' => true,

                ]);
          $test->translation()->firstOrCreate(
              [
                  'language_id' => 102,
                  'title' => "Թեստ $id"
              ]
          );
        }
    }
}
