<?php

namespace App\Service;

use App\Http\Resources\ChoiceResource;
use App\Interface\Repository\ChoiceRepositoryInterface;
use App\Interface\Repository\QuestionRepositoryInterface;
use App\Interface\Service\ChoiceServiceInterface;
use Illuminate\Http\JsonResponse;

class ChoiceService implements ChoiceServiceInterface
{
    public function __construct(
        private ChoiceRepositoryInterface   $choiceRepository,
        private QuestionRepositoryInterface $questionRepository,
    ) {}

    public function create(object $data, int $questionId, int $expertId): JsonResponse
    {
        $question = $this->questionRepository->getById($questionId);
        $this->assertOwnership($question, $expertId);

        $choice = $this->choiceRepository->create($data, $questionId);
        return response()->json(new ChoiceResource($choice), 201);
    }

    public function update(object $data, int $id, int $expertId): JsonResponse
    {
        $choice = $this->choiceRepository->getById($id);
        $this->assertOwnershipViaChoice($choice, $expertId);

        $updated = $this->choiceRepository->update($data, $id);
        return response()->json(new ChoiceResource($updated));
    }

    public function delete(int $id, int $expertId): JsonResponse
    {
        $choice = $this->choiceRepository->getById($id);
        $this->assertOwnershipViaChoice($choice, $expertId);

        $this->choiceRepository->delete($id);
        return response()->json(['message' => 'Choice deleted successfully.']);
    }

    private function assertOwnership(object $question, int $expertId): void
    {
        if ((int) $question->questionnaire->expert_id !== $expertId) {
            abort(403, 'You do not have permission to modify this question.');
        }
    }

    private function assertOwnershipViaChoice(object $choice, int $expertId): void
    {
        // choice->question->questionnaire is eagerly loaded in repository
        if ((int) $choice->question->questionnaire->expert_id !== $expertId) {
            abort(403, 'You do not have permission to modify this choice.');
        }
    }
}
