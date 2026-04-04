<?php

namespace App\Service;

use App\Http\Resources\QuestionnaireListResource;
use App\Http\Resources\QuestionnaireDetailResource;
use App\Interface\Repository\QuestionnaireRepositoryInterface;
use App\Interface\Service\QuestionnaireServiceInterface;
use Illuminate\Http\JsonResponse;

class QuestionnaireService implements QuestionnaireServiceInterface
{
    public function __construct(
        private QuestionnaireRepositoryInterface $questionnaireRepository,
    ) {}

    // ── Public ──────────────────────────────────────────────────────────────

    public function getAllPublic(): JsonResponse
    {
        $questionnaires = $this->questionnaireRepository->getAllPublic();
        return response()->json(QuestionnaireListResource::collection($questionnaires));
    }

    public function getPublicById(int $id): JsonResponse
    {
        $questionnaire = $this->questionnaireRepository->getPublicById($id);
        return response()->json(new QuestionnaireDetailResource($questionnaire));
    }

    // ── Expert ───────────────────────────────────────────────────────────────

    public function getAllForExpert(int $expertId): JsonResponse
    {
        $questionnaires = $this->questionnaireRepository->getAllForExpert($expertId);
        return response()->json(QuestionnaireListResource::collection($questionnaires));
    }

    public function getById(int $id, int $expertId): JsonResponse
    {
        $questionnaire = $this->questionnaireRepository->getById($id);
        $this->assertOwnership($questionnaire, $expertId);

        return response()->json(new QuestionnaireDetailResource($questionnaire));
    }

    public function create(object $data, int $expertId): JsonResponse
    {
        $questionnaire = $this->questionnaireRepository->create($data, $expertId);
        return response()->json(new QuestionnaireDetailResource($questionnaire), 201);
    }

    public function update(object $data, int $id, int $expertId): JsonResponse
    {
        $questionnaire = $this->questionnaireRepository->getById($id);
        $this->assertOwnership($questionnaire, $expertId);

        $updated = $this->questionnaireRepository->update($data, $id);
        return response()->json(new QuestionnaireDetailResource($updated));
    }

    public function publish(int $id, int $expertId): JsonResponse
    {
        $questionnaire = $this->questionnaireRepository->getById($id);
        $this->assertOwnership($questionnaire, $expertId);

        // Enforce structural validation before publishing
        $errors = $this->validateForPublish($questionnaire);
        if (! empty($errors)) {
            return response()->json(['message' => 'Questionnaire is not ready to publish.', 'errors' => $errors], 422);
        }

        $updated = $this->questionnaireRepository->publish($id);
        return response()->json(new QuestionnaireDetailResource($updated));
    }

    public function toggleVisibility(int $id, bool $isVisible, int $expertId): JsonResponse
    {
        $questionnaire = $this->questionnaireRepository->getById($id);
        $this->assertOwnership($questionnaire, $expertId);

        // Cannot make visible if not published
        if ($isVisible && $questionnaire->status !== 'published') {
            return response()->json(['message' => 'Cannot make a draft questionnaire visible.'], 422);
        }

        $updated = $this->questionnaireRepository->toggleVisibility($id, $isVisible);
        return response()->json(new QuestionnaireDetailResource($updated));
    }

    public function delete(int $id, int $expertId): JsonResponse
    {
        $questionnaire = $this->questionnaireRepository->getById($id);
        $this->assertOwnership($questionnaire, $expertId);

        $this->questionnaireRepository->delete($id);
        return response()->json(['message' => 'Questionnaire deleted successfully.']);
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function assertOwnership(object $questionnaire, int $expertId): void
    {
        // Abort with 403 if the authenticated expert doesn't own this resource
        if ((int) $questionnaire->expert_id !== $expertId) {
            abort(403, 'You do not have permission to modify this questionnaire.');
        }
    }

    private function validateForPublish(object $questionnaire): array
    {
        $errors = [];

        $questionCount = $questionnaire->questions->count();
        if ($questionCount < 5) {
            $errors[] = "Questionnaire must have at least 5 questions. Currently has {$questionCount}.";
        }

        foreach ($questionnaire->questions as $question) {
            if ($question->choices->count() < 2) {
                $errors[] = "Question \"{$question->body}\" must have at least 2 choices.";
            }
        }

        if ($questionnaire->severityLevels->count() < 2) {
            $errors[] = 'Questionnaire must have at least 2 severity levels.';
        }

        if ($questionnaire->has_essay_question && empty($questionnaire->essay_prompt)) {
            $errors[] = 'Essay prompt is required when essay question is enabled.';
        }

        return $errors;
    }
}
