<?php

namespace App\Http\Controllers\Api\Expert;

use App\Http\Controllers\Controller;
use App\Interface\Service\QuizSessionServiceInterface;
use Illuminate\Http\JsonResponse;

class ResultController extends Controller
{
    public function __construct(
        private QuizSessionServiceInterface $sessionService,
    ) {}

    // GET /api/expert/questionnaires/{questionnaireId}/results
    public function index(int $questionnaireId): JsonResponse
    {
        return $this->sessionService->getCompletedForQuestionnaire($questionnaireId, auth()->id());
    }
}
