<?php

namespace App\Interface\Service;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

interface QuizSessionServiceInterface
{
    public function create(int $questionnaireId): JsonResponse;

    public function submitResponses(string $uuid, array $responses): JsonResponse;

    // Computes score, matches severity, triggers AI, marks complete
    public function complete(string $uuid): JsonResponse;

    public function getResult(string $uuid): JsonResponse;

    public function downloadPdf(string $uuid, ?string $displayName): Response;

    public function getCompletedForQuestionnaire(int $questionnaireId, int $expertId): JsonResponse;
}
