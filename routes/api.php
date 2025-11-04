<?php

use App\Helpers\StorageHelper;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ExamTestController;
use App\Http\Controllers\Api\V1\FavoriteQuestionController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\PasswordResetController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\RoadRulesController;
use App\Http\Controllers\Api\V1\RoadSignsController;
use App\Http\Controllers\Api\V1\StatisticController;
use App\Http\Controllers\Api\V1\TelegramController;
use App\Http\Controllers\Api\V1\UserExamTestController;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/a', function () {
    $allQuestions = Question::query()
        ->whereHas('translationCommand', function ($query) {
            $query->where('language_id', 101);
        })
        ->get();

   $t =  \App\Models\QuestionTranslation::query()
        ->where('language_id', 101)
        ->get();
    dd($allQuestions,   $t );
});

Route::prefix('v1')->group(function () {

    Route::group([
        'prefix' => '{lang}',
        'middleware' => ['locale'],
        'where' => ['lang' => 'en|hy|ru']
    ], function () {

        Route::post('/telegram/webhook', [TelegramController::class, 'handle']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/social-login', [AuthController::class, 'socialLogin']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/password/forgot', [PasswordResetController::class, 'forgotPassword']);
        Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);
        Route::post('/questions/favorite', [FavoriteQuestionController::class, 'getFavoriteQuestions']);

        Route::get('/tests', [ExamTestController::class, 'index']);
        Route::post('/tests/{testId}', [UserExamTestController::class, 'generateUuId']);
        Route::post('/tests/{uniqueId}/answer', [UserExamTestController::class, 'submitAnswer'])->name('v1.exam.answer');
        Route::post('/tests/{uniqueId}/final', [UserExamTestController::class, 'submitFinal'])->name('v1.exam.answer');
        Route::get('/tests/{uniqueId}', [UserExamTestController::class, 'show'])->name('v1.exam.show');
        Route::get('/my-tests/{testId}', [ExamTestController::class, 'testByUuId']);


        Route::middleware('auth:api')->group(function () {
            Route::post('/favorites/toggle', [FavoriteQuestionController::class, 'toggle']);
            Route::get('/favorites', [FavoriteQuestionController::class, 'list']);

            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::put('/password', [ProfileController::class, 'changePassword'])->name('user.update');

            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::get('/user', [AuthController::class, 'user']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/statistic', [StatisticController::class, 'store']);
            Route::get('/statistic/users/{userId}/group/{date}', [StatisticController::class, 'getByDate'])->middleware('admin');
            Route::get('/statistic/users/{userId}', [StatisticController::class, 'getUserGroupList'])->middleware('admin');
            Route::get('/statistic/users', [StatisticController::class, 'getGroupUserList'])->middleware('admin');
            Route::PATCH('/statistic/{id}/{isCorrect}', [StatisticController::class, 'updateStatistic']);
            Route::get('/my-tests', [ExamTestController::class, 'myTests']);
            Route::get('/my-tests/user/{userId}', [ExamTestController::class, 'testsByUserId'])->middleware('admin');


        });
        Route::get('/road-signs', [RoadSignsController::class, 'index']);
        Route::get('/road-rules', [RoadRulesController::class, 'index']);
        Route::get('/exam-groups', [GroupController::class, 'index'])->name('v2.exam-groups.index');
        Route::get('/exam-groups/{groupId}/questions', [GroupController::class, 'questions'])->name('v2.exam-groups.questions');;
    });

    Route::get('/as', function () {
        $data = [];

        foreach (range(1, 62) as $id) {

            $url = "https://www.drift.am/test/category/$id";


            $html = file_get_contents($url);

// Force UTF-8 encoding
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML($html);
            $xpath = new DOMXPath($dom);

// Find all question blocks
            $questionBlocks = $xpath->query('//div[contains(@class, "col-12 question")]');


            foreach ($questionBlocks as $block) {
                $blockXpath = new DOMXPath($block->ownerDocument);

                // Extract question text
                $questionNode = $blockXpath->query('.//h3[@class="question-text"]', $block)->item(0);
                $questionText = $questionNode ? trim($questionNode->textContent) : '';

                // Extract answers
                $labels = $blockXpath->query('.//label[@type="button"]', $block);
                $answers = [];
                foreach ($labels as $label) {
                    $answers[] = trim($label->textContent);
                }

                $data[] = [
                    'test_id' => $id,
                    'question' => $questionText,
                    'answers' => $answers,
                ];
            }
        }
// Set correct headers if outputting directly
        $filePath = storage_path('app/questions.json');
        file_put_contents($filePath, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $this->info("✅ All questions saved to: $filePath");
    });


    Route::get('/nn', function () {
        $url = "https://www.drift.am/trafficsigns";
        $html = file_get_contents($url);

        // Force UTF-8 encoding
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);


        // Select tables with the class "col-12 question"
        $questionBlocks = $xpath->query('//table[contains(@class, "table traffic-table col-md-9")]');
        $results = [];

        foreach ($questionBlocks as $table) {
            $rows = [];

            foreach ($table->getElementsByTagName('tr') as $tr) {
                $cells = [];

                foreach ($tr->getElementsByTagName('td') as $td) {
                    $cell = [
                        'text' => trim($td->textContent),
                        'image' => null,
                    ];

                    // Try to extract image
                    $imgTag = $td->getElementsByTagName('img')->item(0);
                    if ($imgTag && $imgTag->hasAttribute('src')) {
                        $src = $imgTag->getAttribute('src');

                        // Convert relative image URLs to absolute
                        if (!str_starts_with($src, 'http')) {
                            $parsedUrl = parse_url($url);
                            $base = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                            $src = $base . '/' . ltrim($src, '/');
                        }

                        // Download and upload image
                        try {
                            // Download to temp file
                            $tempImage = tempnam(sys_get_temp_dir(), 'img_');
                            file_put_contents($tempImage, file_get_contents($src));

                            $ext = pathinfo(parse_url($src, PHP_URL_PATH), PATHINFO_EXTENSION);
                            if ($ext) {
                                rename($tempImage, $tempImage . '.' . $ext);
                                $tempImage .= '.' . $ext;
                            }

                            // Upload file
                            $upload = StorageHelper::uploadFile($tempImage, 'road-signs');
                            $cell['image'] = $upload['file_name'] ?? null;

                            // Clean up temp file
                            @unlink($tempImage);
                        } catch (\Exception $e) {
                            $cell['image'] = null;
                        }
                    }

                    $cells[] = $cell;
                }

                // Ensure there are at least 3 cells before accessing them
                if (count($cells) >= 3) {
                    $title = $cells[0]['text'];
                    $image = $cells[1]['image'];
                    $description = $cells[2]['text'];

                    $rows[] = [
                        'title' => $title,
                        'image' => $image,
                        'description' => $description,
                    ];
                }
            }

            // Add the rows to results if not empty
            if (!empty($rows)) {
                $results[] = $rows;
            }
        }

        // Save to JSON file
        $json = json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        Storage::disk('public')->put('traffic_signs.json', $json);

        return response()->json([
            'message' => 'Data extracted and saved successfully.',
            'file' => Storage::url('traffic_signs.json'),
        ]);
    });


    Route::get('/aaa', function () {

        function getInnerHtml($node)
        {
            $innerHTML = '';
            $children = $node->childNodes;
            foreach ($children as $child) {
                $innerHTML .= $child->ownerDocument->saveHTML($child);
            }
            return $innerHTML;
        }

        $url = "https://automotorschool.am/roadrules/";

        $html = file_get_contents($url);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $sections = [];

        $accordionItems = $xpath->query('//div[contains(@class, "elementor-accordion-item")]');

        foreach ($accordionItems as $item) {
            // Extract title
            $titleElement = $xpath->query('.//a[contains(@class, "elementor-accordion-title")]', $item)->item(0);
            $title = $titleElement ? trim($titleElement->textContent) : '';

            // Extract subsections
            $contentDiv = $xpath->query('.//div[contains(@class, "elementor-tab-content")]', $item)->item(0);
            $subsections = [];

            if ($contentDiv) {
                $fullLengthDivs = $xpath->query('.//div[contains(@class, "full-length")]', $contentDiv);
                foreach ($fullLengthDivs as $div) {
                    // Get HTML content
                    $html = getInnerHtml($div);
                    // Get plain text content, cleaned up
                    $text = preg_replace('/\s+/', ' ', $div->textContent);
                    $text = trim($text);
                    // Store both HTML and text in subsection
                    $subsections[] = [
                        'html' => $html,
                        'text' => $text
                    ];
                }
            }

            // Add section to array
            $sections[] = [
                'title' => $title,
                'subsections' => $subsections,
            ];
        }

        libxml_clear_errors();
        libxml_use_internal_errors(false);

// Convert to JSON and output
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($sections, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    }); // test route


    Route::get('/aasa', function () {
        $groups = \App\Models\Group::query()->with('questions')->get();

        foreach ($groups as $group) {
            $groupFolder = "groups/{$group->id}";

            // Create group folder if it doesn't exist
            if (!Storage::disk('public')->exists($groupFolder)) {
                Storage::disk('public')->makeDirectory($groupFolder);
            }

            foreach ($group->questions as $question) {
                if ($question->image) {
                    // Extract filename from image URL
                    $filename = basename(parse_url($question->image, PHP_URL_PATH)); // e.g., 2e14d0d9-....png
                    $extension = pathinfo($filename, PATHINFO_EXTENSION);

                    // Path relative to storage/app/public
                    $sourcePath = "questions/{$filename}";
                    $targetPath = "groups/{$group->id}/{$question->id}.{$extension}";

                    if (Storage::disk('public')->exists($sourcePath)) {
                        Storage::disk('public')->copy($sourcePath, $targetPath);
                    } else {
                        logger()->warning("Missing file: {$sourcePath} for question {$question->id}");
                    }
                }
            }

        }
    });


    Route::get('/send-message', function () {
        $chatId = '-4828510689'; // Replace with your chat ID
        $message = 'Hello, this is a message from Laravel!';

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        return 'Message sent to Telegram!';
    });

    Route::get('/get-updates', function () {
        $updates = Telegram::getUpdates();
        return $updates;
    });
    Route::post('telegram/callback', [TelegramController::class, 'handleCallback']);
});
