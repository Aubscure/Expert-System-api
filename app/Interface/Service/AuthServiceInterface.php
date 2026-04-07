<?php

namespace App\Interface\Service;

use Illuminate\Http\JsonResponse;

interface AuthServiceInterface
{
    public function login(object $data): JsonResponse;

    public function validateInvitation(object $data): JsonResponse;

    public function registerExpert(object $data): JsonResponse;

    public function logout(): JsonResponse;
}
