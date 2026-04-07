<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterExpertRequest;
use App\Interface\Service\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthServiceInterface $authService,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login($request);
    }

    public function validateInvitation(Request $request): JsonResponse
    {
        return $this->authService->validateInvitation((object) ['token' => $request->query('token')]);
    }

    public function register(RegisterExpertRequest $request): JsonResponse
    {
        return $this->authService->registerExpert($request);
    }

    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }
}
