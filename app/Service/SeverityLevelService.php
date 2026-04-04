<?php

namespace App\Service;

use App\Http\Resources\SeverityLevelResource;
use App\Interface\Repository\QuestionnaireRepositoryInterface;
use App\Interface\Repository\SeverityLevelRepositoryInterface;
use App\Interface\Service\SeverityLevelServiceInterface;
use Illuminate\Http\JsonResponse;

class SeverityLevelService implements SeverityLevelServiceInterface
{
    public function __construct(
        private SeverityLevelRepositoryInterface $severityLevelRepository,
        private QuestionnaireRepositoryInterface $questionnaireRepository,
    ) {}

    public function getAllForQuestionnaire(int $questionnaireId): JsonResponse
    {
        $levels = $this->severityLevelRepository->getAllForQuestionnaire($questionnaireId);
        return response()->json(SeverityLevelResource::collection($levels));
    }

    public function create(object $data, int $questionnaireId, int $expertId): JsonResponse
    {
        $questionnaire = $this->questionnaireRepository->getById($questionnaireId);
        $this->assertOwnership($questionnaire, $expertId);

        $level = $this->severityLevelRepository->create($data, $questionnaireId);
        return response()->json(new SeverityLevelResource($level), 201);
    }

    public function update(object $data, int $id, int $expertId): JsonResponse
    {
        $level = $this->severityLevelRepository->getById($id);
        $this->assertOwnership($level->questionnaire, $expertId);

        $updated = $this->severityLevelRepository->update($data, $id);
        return response()->json(new SeverityLevelResource($updated));
    }

    public function delete(int $id, int $expertId): JsonResponse
    {
        $level = $this->severityLevelRepository->getById($id);
        $this->assertOwnership($level->questionnaire, $expertId);

        $this->severityLevelRepository->delete($id);
        return response()->json(['message' => 'Severity level deleted successfully.']);
    }

    private function assertOwnership(object $questionnaire, int $expertId): void
    {
        if ((int) $questionnaire->expert_id !== $expertId) {
            abort(403, 'You do not have permission to modify this questionnaire.');
        }
    }
}
