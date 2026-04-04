<?php

namespace App\Interface\Service;

use Illuminate\Http\JsonResponse;

interface SeverityLevelServiceInterface
{
    public function getAllForQuestionnaire(int $questionnaireId): JsonResponse;

    public function create(object $data, int $questionnaireId, int $expertId): JsonResponse;

    public function update(object $data, int $id, int $expertId): JsonResponse;

    public function delete(int $id, int $expertId): JsonResponse;
}
