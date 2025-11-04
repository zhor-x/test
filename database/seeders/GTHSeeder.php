<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Smalot\PdfParser\Parser;

class GTHSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pdf = public_path('74752e63fc8b360eb4e4c84bb5d709f5.pdf');
        $parser = new Parser();
        $tempDir = storage_path('app/temp/images');
        $saveDir = storage_path('app/public/questions');
    }
}
