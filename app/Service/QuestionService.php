<?php

namespace App\Service;

use App\Http\Resources\QuestionResource;
use App\Interface\Repository\QuestionRepositoryInterface;
use App\Interface\Repository\QuestionnaireRepositoryInterface;
use App\Interface\Service\QuestionServiceInterface;
use Illuminate\Http\JsonResponse;

class QuestionService implements QuestionServiceInterface
{
    public function __construct(
        private QuestionRepositoryInterface      $questionRepository,
        private QuestionnaireRepositoryInterface $questionnaireRepository,
    ) {}

    public function create(object $data, int $questionnaireId, int $expertId): JsonResponse
    {
        // Verify the expert owns the parent questionnaire before adding a question
        $questionnaire = $this->questionnaireRepository->getById($questionnaireId);
        $this->assertQuestionnaireOwnership($questionnaire, $expertId);

        $question = $this->questionRepository->create($data, $questionnaireId);
        return response()->json(new QuestionResource($question), 201);
    }

    public function update(object $data, int $id, int $expertId): JsonResponse
    {
        $question = $this->questionRepository->getById($id);
        $this->assertOwnershipViaQuestion($question, $expertId);

        $updated = $this->questionRepository->update($data, $id);
        return response()->json(new QuestionResource($updated));
    }

    public function delete(int $id, int $expertId): JsonResponse
    {
        $question = $this->questionRepository->getById($id);
        $this->assertOwnershipViaQuestion($question, $expertId);

        $questionnaireId = $question->questionnaire_id;
        $this->questionRepository->delete($id);

        // Re-sequence order_index to fill the gap left by deletion
        $this->questionRepository->reorder($questionnaireId);

        return response()->json(['message' => 'Question deleted successfully.']);
    }

    private function assertQuestionnaireOwnership(object $questionnaire, int $expertId): void
    {
        if ((int) $questionnaire->expert_id !== $expertId) {
            abort(403, 'You do not have permission to modify this questionnaire.');
        }
    }

    private function assertOwnershipViaQuestion(object $question, int $expertId): void
    {
        // question->questionnaire is eager-loaded in the repository (only expert_id selected)
        if ((int) $question->questionnaire->expert_id !== $expertId) {
            abort(403, 'You do not have permission to modify this question.');
        }
    }
}
