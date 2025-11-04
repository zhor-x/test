<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Answer;
use App\Models\AnswerTranslation;
use App\Models\Question;
use App\Models\QuestionTranslation;
use App\Models\GroupTranslation;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestTranslation;
use App\Models\Explanation;
use App\Models\ExplanationTranslation;
use App\Models\Language;
use App\Models\UserExamTest;
use App\Models\UserExamTestQuestion;
use Illuminate\Database\Seeder;

// Add this missing import

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Languages
        $languages = [
            ['id' => 100, 'flag' => 'en.jpg', 'country_code' => 'en-us', 'country' => 'USA'],
            ['id' => 101, 'flag' => 'ru.jpg', 'country_code' => 'ru-ru', 'country' => 'Russian'],
            ['id' => 102, 'flag' => 'am.jpg', 'country_code' => 'hy-am', 'country' => 'Armenian'],
        ];

        foreach ($languages as $lang) {
            Language::updateOrCreate(['id' => $lang['id']], $lang);
        }
        $groupData = [
            [
                'id' => 1,
                'translations' => [
                    [
                        'title' => 'Խումբ 1', 'description' => 'Մանևրում, դասավորվածություն երթևեկելի մասում, երթևեկության առավելություն'
                    ],

                ]
            ], [
                'id' => 2,
                'translations' => [
                    [
                        'title' => 'Խումբ 2', 'description' => 'ՀՀ օրենք «Ճանապարհային երթևեկության անվտանգության ապահովման մասին»:'
                    ],
                ]
            ],
            [
                'id' => 3,
                'translations' => [
                    [
                        'title' => 'Խումբ 3', 'description' => 'Տրանսպորտային միջոցների շահագործումն արգելող անսարքությունները և պայմանները:'
                    ],

                ]
            ], [
                'id' => 4,
                'translations' => [
                    [
                        'title' => 'Խումբ 4', 'description' => 'Ճանապարհային նշաններ և ճանապարհային գծանշումներ:'
                    ],
                ]
            ], [
                'id' => 5,
                'translations' => [
                    [
                        'title' => 'Խումբ 5', 'description' => 'Մանևրում, դասավորվածություն երթևեկելի մասում, երթևեկության առավելություն'
                    ],
                ]
            ], [
                'id' => 6,
                'translations' => [
                    [
                        'title' => 'Խումբ 6', 'description' => 'Մանևրում, դասավորվածություն երթևեկելի մասում, երթևեկության առավելություն'
                    ],
                ]
            ], [
                'id' => 7,
                'translations' => [
                    [
                        'title' => 'Խումբ 7', 'description' => 'Մանևրում, դասավորվածություն երթևեկելի մասում, երթևեկության առավելություն'
                    ],

                ]
            ], [
                'id' => 8,
                'translations' => [
                    [
                        'title' => 'Խումբ 8', 'description' => 'Մանևրում, դասավորվածություն երթևեկելի մասում, երթևեկության առավելություն'
                    ],
                ]
            ], [
                'id' => 9,
                'translations' => [
                    [
                        'title' => 'Խումբ 9', 'description' => 'Մանևրում, դասավորվածություն երթևեկելի մասում, երթևեկության առավելություն'
                    ],
                ]
            ], [
                'id' => 10,
                'translations' => [
                    [
                        'title' => 'Խումբ 10', 'description' => 'Մանևրում, դասավորվածություն երթևեկելի մասում, երթևեկության առավելություն'
                    ],
                ]
            ],
        ];


        foreach ($groupData as $group){
              Group::firstOrCreate(['id' => $group['id']]);
            GroupTranslation::firstOrCreate(
                [
                    'group_id' => $group['id'],
                    'language_id' => 102,
                    'title' => $group['translations'][0]['title'],
                    'description' => $group['translations'][0]['description'],

                ]
            );


        }



        // Exam Test
//        $examTest = ExamTest::create([
//            'id' => 321274,
//            'duration' => 30,
//            'max_wrong_answers' => 2,
//            'is_valid' => true,
//        ]);

        // Exam Test Translations
//        $translations = [
//            ['test_id' => 321274, 'language_id' => 213, 'title' => 'Test 1'],
//            ['test_id' => 321274, 'language_id' => 214, 'title' => 'Тест 1'],
//            ['test_id' => 321274, 'language_id' => 215, 'title' => 'Թեստ 1'],
//        ];
//        foreach ($translations as $trans) {
//            ExamTestTranslation::create($trans);
//        }

        $this->call([NewQuestionSeeder::class, TestSeeder::class]);
        // Exam Group Question
//        $question = groupQuestion::create([
//            'id' => 321256,
//            'image' => 'aee63a5c-d169-40ca-9d26-b330dd4f69aa.png',
//            'group_id' => 321200,
//        ]);
//
//        // Question Translation
//        groupQuestionTranslation::create([
//            'question_id' => 321256,
//            'language_id' => 215,
//            'title' => 'Դուք սկսում եք երթեւեկել ճամփեզրից, պարտավո՞ր եք արդյոք զիջել ճանապարհը թեթեւ մարդատար ավտոմոբիլին, որը կատարում է հետադարձ։',
//        ]);

//        // Answers
//        $answers = [
//            ['id' => 321350, 'is_right' => true, 'group_id' => 321200, 'question_id' => 321256],
//            ['id' => 321351, 'is_right' => false, 'group_id' => 321200, 'question_id' => 321256],
//            ['id' => 3451839, 'is_right' => true, 'group_id' => 321200, 'question_id' => 321256], // Added for exam_test_answer_id
//        ];
//        foreach ($answers as $ans) {
//            groupAnswer::create($ans);
//        }
//
//        // Answer Translations
//        groupAnswerTranslation::create([
//            'answer_id' => 321350,
//            'language_id' => 215,
//            'title' => 'Պարտավոր եք։',
//        ]);
//        groupAnswerTranslation::create([
//            'answer_id' => 321351,
//            'language_id' => 215,
//            'title' => 'Պարտավոր չեք։',
//        ]);
//        groupAnswerTranslation::create([
//            'answer_id' => 3451839,
//            'language_id' => 215,
//            'title' => 'Պարտավոր եք։', // Added for consistency
//        ]);

        // Explanation
//        $explanation = Explanation::create([
//            'id' => 321256,
//            'group_id' => 321200,
//            'question_id' => 321256,
//        ]);

        // Explanation Translation
//        ExplanationTranslation::create([
//            'explanation_id' => 321256,
//            'language_id' => 215,
//            'title' => ' ',
//            'description' => ' ',
//        ]);

        // Exam Test Question
//        $examTestQuestion = ExamTestQuestion::create([
//            'id' => 801418,
//            'test_id' => 321274,
//            'question_id' => 321256,
//        ]);

        // User Exam Test
//        $userExamTest = UserExamTest::create([
//            'id' => 3458434506,
//            'test_id' => 321274,
//            'unique_id' => 'rc9e0qlhq:04f44218-2233-4e6e-80fd-b55f46020ca3',
//            'is_completed' => false,
//        ]);

        // User Exam Test Question
//        UserExamTestQuestion::create([
//            'id' => 13564685159,
//            'user_test_id' => 3458434506,
//            'exam_test_question_id' => 801418,
//            'exam_test_answer_id' => 3451839,
//            'is_right' => true,
//        ]);
    }
}
