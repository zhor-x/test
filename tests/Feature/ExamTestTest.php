<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(); // Run the seeder before each test
});

test('v2 exam index returns success with exam tests', function () {
    $response = $this->getJson('/api/v2/exam');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'exam_tests' => [
                '*' => [
                    'id',
                    'duration',
                    'max_wrong_answers',
                    'is_valid',
                    'deleted_at',
                    'created_at',
                    'updated_at',
                    'translation' => [
                        'id',
                        'test_id',
                        'language_id',
                        'title',
                        'created_at',
                        'updated_at',
                        'language' => [
                            'id',
                            'flag',
                            'country_code',
                            'country',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'translations' => [
                        '*' => [
                            'id',
                            'test_id',
                            'language_id',
                            'title',
                            'created_at',
                            'updated_at',
                            'language',
                        ],
                    ],
                ],
            ],
            'status',
        ]);
});

test('v2 user exam test show returns success with user exam test data', function () {
    $response = $this->getJson('/api/v2/exam/rc9e0qlhq:04f44218-2233-4e6e-80fd-b55f46020ca3');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'user_exam_test' => [
                'id',
                'user_id',
                'test_id',
                'unique_id',
                'is_completed',
                'finish_time',
                'created_at',
                'updated_at',
                'exam_test' => [
                    'id',
                    'duration',
                    'max_wrong_answers',
                    'is_valid',
                    'deleted_at',
                    'created_at',
                    'updated_at',
                    'translation',
                    'questions' => [
                        '*' => [
                            'id',
                            'image',
                            'group_id',
                            'created_at',
                            'updated_at',
                            'pivot',
                            'answers' => [
                                '*' => [
                                    'id',
                                    'is_right',
                                    'group_id',
                                    'question_id',
                                    'created_at',
                                    'updated_at',
                                    'translation',
                                ],
                            ],
                            'translation',
                            'explanation' => [
                                'id',
                                'group_id',
                                'question_id',
                                'created_at',
                                'updated_at',
                                'translation',
                            ],
                        ],
                    ],
                ],
                'user_exam_test_questions' => [
                    '*' => [
                        'id',
                        'user_test_id',
                        'exam_test_question_id',
                        'exam_test_answer_id',
                        'is_right',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ],
        ]);
});

test('v2 user exam test show returns 404 for invalid unique ID', function () {
    $response = $this->getJson('/api/v2/exam/invalid-unique-id');

    $response->assertStatus(404)
        ->assertJson([
            'error' => 'User exam test not found',
        ]);
});
