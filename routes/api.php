<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Public\QuestionnaireController as PublicQuestionnaireController;
use App\Http\Controllers\Api\Public\QuizSessionController;
use App\Http\Controllers\Api\Expert\QuestionnaireController as ExpertQuestionnaireController;
use App\Http\Controllers\Api\Expert\QuestionController;
use App\Http\Controllers\Api\Expert\ChoiceController;
use App\Http\Controllers\Api\Expert\SeverityLevelController;
use App\Http\Controllers\Api\Expert\ResultController;
use App\Http\Controllers\Api\Admin\StatsController;
use App\Http\Controllers\Api\Admin\InvitationController;
use App\Http\Controllers\Api\Admin\ExpertController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;

// ── Rate Limiters ──────────────────────────────────────────────────────────────
// Defined here — applied via throttle middleware below

RateLimiter::for('login', function (Request $request) {
    // 5 attempts per minute per IP — prevents brute force
    return Limit::perMinute(5)->by($request->ip());
});

RateLimiter::for('session-create', function (Request $request) {
    // 20 new sessions per hour per IP — prevents session flooding
    return Limit::perHour(20)->by($request->ip());
});

// ── Public routes (no authentication) ─────────────────────────────────────────

Route::get('/questionnaires',      [PublicQuestionnaireController::class, 'index']);
Route::get('/questionnaires/{id}', [PublicQuestionnaireController::class, 'show']);

Route::get('/invitations/validate', [AuthController::class, 'validateInvitation']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login'])->middleware('throttle:login');

Route::middleware('throttle:session-create')
    ->post('/sessions', [QuizSessionController::class, 'store']);

Route::post('/sessions/{uuid}/responses', [QuizSessionController::class, 'submitResponses']);
Route::post('/sessions/{uuid}/complete',  [QuizSessionController::class, 'complete']);
Route::get('/sessions/{uuid}/result',     [QuizSessionController::class, 'result']);
Route::get('/sessions/{uuid}/pdf',        [QuizSessionController::class, 'downloadPdf']);

// ── Expert routes ──────────────────────────────────────────────────────────────

Route::middleware(['auth:sanctum', 'abilities:expert'])
    ->prefix('expert')
    ->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // Questionnaire CRUD
        Route::get('/questionnaires',        [ExpertQuestionnaireController::class, 'index']);
        Route::post('/questionnaires',       [ExpertQuestionnaireController::class, 'store']);
        Route::get('/questionnaires/{id}',   [ExpertQuestionnaireController::class, 'show']);
        Route::put('/questionnaires/{id}',   [ExpertQuestionnaireController::class, 'update']);
        Route::delete('/questionnaires/{id}',[ExpertQuestionnaireController::class, 'destroy']);

        // Publish and visibility actions
        Route::patch('/questionnaires/{id}/publish',    [ExpertQuestionnaireController::class, 'publish']);
        Route::patch('/questionnaires/{id}/visibility', [ExpertQuestionnaireController::class, 'toggleVisibility']);

        // Questions (nested under questionnaire)
        Route::post('/questionnaires/{questionnaireId}/questions', [QuestionController::class, 'store']);
        Route::put('/questions/{id}',                              [QuestionController::class, 'update']);
        Route::delete('/questions/{id}',                           [QuestionController::class, 'destroy']);

        // Choices (nested under question)
        Route::post('/questions/{questionId}/choices', [ChoiceController::class, 'store']);
        Route::put('/choices/{id}',                    [ChoiceController::class, 'update']);
        Route::delete('/choices/{id}',                 [ChoiceController::class, 'destroy']);

        // Severity levels (nested under questionnaire)
        Route::get('/questionnaires/{questionnaireId}/severity-levels',  [SeverityLevelController::class, 'index']);
        Route::post('/questionnaires/{questionnaireId}/severity-levels', [SeverityLevelController::class, 'store']);
        Route::put('/severity-levels/{id}',                              [SeverityLevelController::class, 'update']);
        Route::delete('/severity-levels/{id}',                           [SeverityLevelController::class, 'destroy']);

        // Results (anonymized — expert sees scores but no identity)
        Route::get('/questionnaires/{questionnaireId}/results', [ResultController::class, 'index']);
    });

// ── Admin routes ───────────────────────────────────────────────────────────────

Route::middleware(['auth:sanctum', 'abilities:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/stats', [StatsController::class, 'index']);

        Route::get('/experts',        [ExpertController::class, 'index']);
        Route::delete('/experts/{id}',[ExpertController::class, 'destroy']);

        Route::get('/invitations',        [InvitationController::class, 'index']);
        Route::post('/invitations',       [InvitationController::class, 'store']);
        Route::delete('/invitations/{id}',[InvitationController::class, 'destroy']);
    });
