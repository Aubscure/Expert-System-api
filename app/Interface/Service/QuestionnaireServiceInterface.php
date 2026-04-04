<?php

namespace App\Interface\Service;

use Illuminate\Http\JsonResponse;

interface QuestionnaireServiceInterface
{
    public function getAllPublic(): JsonResponse;

    public function getPublicById(int $id): JsonResponse;

    public function getAllForExpert(int $expertId): JsonResponse;

    public function getById(int $id, int $expertId): JsonResponse;

    public function create(object $data, int $expertId): JsonResponse;

    public function update(object $data, int $id, int $expertId): JsonResponse;

    public function publish(int $id, int $expertId): JsonResponse;

    public function toggleVisibility(int $id, bool $isVisible, int $expertId): JsonResponse;

    public function delete(int $id, int $expertId): JsonResponse;
}
