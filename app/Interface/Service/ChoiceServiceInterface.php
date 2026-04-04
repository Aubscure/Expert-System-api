<?php

namespace App\Interface\Service;

use Illuminate\Http\JsonResponse;

interface ChoiceServiceInterface
{
    public function create(object $data, int $questionId, int $expertId): JsonResponse;

    public function update(object $data, int $id, int $expertId): JsonResponse;

    public function delete(int $id, int $expertId): JsonResponse;
}
