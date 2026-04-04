<?php

namespace App\Http\Controllers\Api\Expert;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expert\StoreQuestionnaireRequest;
use App\Http\Requests\Expert\UpdateQuestionnaireRequest;
use App\Interface\Service\QuestionnaireServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function __construct(
        private QuestionnaireServiceInterface $questionnaireService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->questionnaireService->getAllForExpert(auth()->id());
    }

    public function store(StoreQuestionnaireRequest $request): JsonResponse
    {
        return $this->questionnaireService->create($request, auth()->id());
    }

    public function show(int $id): JsonResponse
    {
        return $this->questionnaireService->getById($id, auth()->id());
    }

    public function update(UpdateQuestionnaireRequest $request, int $id): JsonResponse
    {
        return $this->questionnaireService->update($request, $id, auth()->id());
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->questionnaireService->delete($id, auth()->id());
    }

    // PATCH /api/expert/questionnaires/{id}/publish
    public function publish(int $id): JsonResponse
    {
        return $this->questionnaireService->publish($id, auth()->id());
    }

    // PATCH /api/expert/questionnaires/{id}/visibility
    public function toggleVisibility(Request $request, int $id): JsonResponse
    {
        $request->validate(['is_visible' => 'required|boolean']);
        return $this->questionnaireService->toggleVisibility($id, $request->boolean('is_visible'), auth()->id());
    }
}
