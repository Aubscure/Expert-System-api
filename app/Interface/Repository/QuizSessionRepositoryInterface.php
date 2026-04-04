<?php

namespace App\Interface\Repository;

interface QuizSessionRepositoryInterface
{
    public function create(int $questionnaireId): object;

    public function getByUuid(string $uuid);

    // Bulk-insert all responses for a session in one transaction
    public function saveResponses(int $sessionId, array $responses): void;

    // Mark session complete and write computed results
    public function complete(string $uuid, int $totalScore, int $severityLevelId, ?string $aiAnalysis): object;

    // Clear essay text after AI has processed it (privacy)
    public function clearEssayText(int $sessionId): void;

    // Admin/Expert analytics
    public function getCompletedForQuestionnaire(int $questionnaireId);

    public function getStats(): array;
}
