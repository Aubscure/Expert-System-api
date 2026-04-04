<?php

namespace App\Interface\Service;

use Illuminate\Http\JsonResponse;

interface AdminServiceInterface
{
    public function getStats(): JsonResponse;

    public function getAllExperts(): JsonResponse;

    public function deactivateExpert(int $id): JsonResponse;

    public function getAllInvitations(): JsonResponse;

    public function createInvitation(?string $email): JsonResponse;

    public function revokeInvitation(int $id): JsonResponse;
}
