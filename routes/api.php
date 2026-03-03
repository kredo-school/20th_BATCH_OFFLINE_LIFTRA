<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GoalController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('goals', GoalController::class)->only([
        'index', // 一覧取得
        'show',  // 個別取得
    ]);
});

// Route::apiResource('milestones', App\Http\Controllers\Api\MilestoneController::class)
//     ->withoutMiddleware('auth:sanctum');　　　//認証を必要とせず動作確認したい時用コード