<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\RoadSignController;
use App\Http\Controllers\Admin\SignGroupController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserGroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

    Route::middleware(['auth:api', 'admin'])->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::post('/logout', function () {
            auth()->logout();
            return response()->json(['message' => 'Successfully logged out']);
        });

        Route::prefix('tests')->group(function () {
            Route::apiResource('/', TestController::class)->parameters(['' => 'test']);
        });

        Route::prefix('users')->group(function () {
            Route::get('/{id}/results', [UserController::class, 'results']);
            Route::apiResource('/', UserController::class)->parameters(['' => 'user']);
        });

        Route::prefix('groups')->group(function () {
            Route::apiResource('/', GroupController::class)->parameters(['' => 'group']);
        });

             Route::apiResource('/road-signs', RoadSignController::class)->parameters(['' => 'road_sign']);
             Route::apiResource('/sign-groups', SignGroupController::class)->parameters(['' => 'group']);

        Route::get('/user-groups/users', [UserController::class, 'groupUserList']);

        Route::apiResource('/user-groups', UserGroupController::class)->parameters(['' => 'group']);


        Route::prefix('questions')->group(function () {
            Route::apiResource('/', QuestionController::class)->parameters(['' => 'question'])
            ->except('update');
            Route::post('/{question}', [QuestionController::class, 'update']);
        });

    });

    Route::post('/login', [AuthController::class, 'login']);

