<?php

namespace App\Http\Controllers\Api\Expert;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expert\StoreQuestionRequest;
use App\Interface\Service\QuestionServiceInterface;
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{
    public function __construct(
        private QuestionServiceInterface $questionService,
    ) {}

    // POST /api/expert/questionnaires/{questionnaireId}/questions
    public function store(StoreQuestionRequest $request, int $questionnaireId): JsonResponse
    {
        return $this->questionService->create($request, $questionnaireId, auth()->id());
    }

    // PUT /api/expert/questions/{id}
    public function update(StoreQuestionRequest $request, int $id): JsonResponse
    {
        return $this->questionService->update($request, $id, auth()->id());
    }

    // DELETE /api/expert/questions/{id}
    public function destroy(int $id): JsonResponse
    {
        return $this->questionService->delete($id, auth()->id());
    }
}
