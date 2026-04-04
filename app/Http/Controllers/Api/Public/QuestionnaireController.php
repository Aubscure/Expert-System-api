<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Interface\Service\QuestionnaireServiceInterface;
use Illuminate\Http\JsonResponse;

class QuestionnaireController extends Controller
{
    public function __construct(
        private QuestionnaireServiceInterface $questionnaireService,
    ) {}

    // GET /api/questionnaires
    public function index(): JsonResponse
    {
        return $this->questionnaireService->getAllPublic();
    }

    // GET /api/questionnaires/{id}
    public function show(int $id): JsonResponse
    {
        return $this->questionnaireService->getPublicById($id);
    }
}
