<?php

namespace App\Console\Commands;

use App\Models\Group;
use App\Models\GroupTranslation;
use App\Models\Language;
use App\Models\PddChapters;
use App\Models\PddChapterTranslation;
use App\Models\PddRule;
use App\Models\PddRuleTranslation;
use App\Models\RoadSignCategoryTranslation;
use App\Models\RoadSignTranslation;
use Illuminate\Console\Command;
use Stichoza\GoogleTranslate\GoogleTranslate;

class AutoTranslatePosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-translate-posts';

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
        $language = Language::query()->where('country_code', 'hy')->first();


        $this->RoadSignCategories($language);
        $this->roadSigns($language);
//
        $this->pddRulesChapters($language);
        $this->group();

    }

    private function RoadSignCategories(Language $language)
    {

        $roadSignCategories = RoadSignCategoryTranslation::query()->where('language_id', $language->id)->get();
        $tr = new GoogleTranslate();

        $arr = [];
        foreach ($roadSignCategories as $roadSignCategory) {
            $titleHy = $roadSignCategory->title;
            $descriptionHy = $roadSignCategory->description;
            $titleRu = $tr->setSource('hy')->setTarget('ru')->translate($titleHy);
            $descriptionRu = $tr->setSource('hy')->setTarget('ru')->translate($descriptionHy);
            $titleEn = $tr->setSource('hy')->setTarget('en')->translate($titleHy);
            $descriptionEn = $tr->setSource('hy')->setTarget('en')->translate($descriptionHy);


            RoadSignCategoryTranslation::query()->firstOrCreate([
                'language_id' => 100,
                'road_sign_category_id' => $roadSignCategory->road_sign_category_id,
                'title' => $titleEn,
                'description' => $descriptionEn,
            ]);
            RoadSignCategoryTranslation::query()->firstOrCreate([
                'language_id' => 101,
                'road_sign_category_id' => $roadSignCategory->road_sign_category_id,
                'title' => $titleRu,
                'description' => $descriptionRu,
            ]);
        }

    }

    private function roadSigns(Language $language)
    {
        $tr = new GoogleTranslate();

        $roadSigns = RoadSignTranslation::query()->where('language_id', $language->id)->get();


        foreach ($roadSigns as $roadSign) {
            $descriptionHy = $roadSign->description;
            $descriptionRu = $tr->setSource('hy')->setTarget('ru')->translate($descriptionHy);
            $descriptionEn = $tr->setSource('hy')->setTarget('en')->translate($descriptionHy);

            RoadSignTranslation::query()->firstOrCreate([
                'language_id' => 100,
                'road_sign_id' => $roadSign->road_sign_id,
                'title' => $roadSign->title,
                'description' => $descriptionEn,
            ]);
            RoadSignTranslation::query()->firstOrCreate([
                'language_id' => 101,
                'road_sign_id' => $roadSign->road_sign_id,
                'title' => $roadSign->title,
                'description' => $descriptionRu,
            ]);
        }
    }

    private function pddRulesChapters(Language $language)
    {
//        PddChapterTranslation::query()->where('language_id', 101)->delete();
//        PddRuleTranslation::query()->where('language_id', 101)->delete();
        $pddChapters = PddChapters::query()->get();
        $filePath = storage_path('app/public/road_rules_ru.json');

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

        foreach ($pddChapters as $key => $pddChapter) {
            $chapterTitleRu = strtoupper($data[$key]['title']);

            // Add or update Russian translation for chapter
            PddChapterTranslation::updateOrCreate(
                [
                    'chapter_id' => $pddChapter->id,
                    'language_id' => 101,
                ],
                [
                    'title' => $chapterTitleRu,
                ]
            );

            // Add Russian translations for existing rules
            $existingRules = $pddChapter->rules()->get(); // assuming relation 'rules()' exists

            foreach ($existingRules as $ruleIndex => $rule) {
                // Match the JSON subsection by index
                $subsection = $data[$key]['subsections'][$ruleIndex] ?? null;
                if (!$subsection) {
                    continue; // skip if no translation available
                }

                PddRuleTranslation::updateOrCreate(
                    [
                        'rule_id' => $rule->id,
                        'language_id' => 101,
                    ],
                    [
                        'content' => $subsection['text'],
                    ]
                );
            }
        }

        $this->info('Russian translations added successfully.');
    }

    private function group()
    {
        $categories = [
            'Маневренность, выравнивание дороги, преимущество в движении.',
            'Закон РА "Об обеспечении безопасности дорожного движения".',
            'Неисправности и условия, запрещающие эксплуатацию транспортных средств.',
            'Дорожные знаки и дорожная разметка.',
            'Перекресток (со знаками, без знаков).',
            'Перекресток (регулятор, светофор).',
            'Дорожная разметка, остановка, станция.',
            'Скорость, буксировка, перевозка людей и грузов.',
            'Предупреждающие сигналы, специальные сигналы, обгон.',
            'О безопасности жизнедеятельности участников дорожного движения и мерах первой помощи и самопомощи при ДТП.',
        ];

        $groups = Group::query()->get();

        foreach ($groups as $key => $group) {
            $newGroup = new GroupTranslation;
            $newGroup->group_id = $group->id;
            $newGroup->language_id = 101;
            $newGroup->title = "Группа " . $key + 1;
            $newGroup->description = $categories[$key];
            $newGroup->save();
        }

    }
}
