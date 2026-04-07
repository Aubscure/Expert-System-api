<?php

namespace App\Service;

use App\Interface\Repository\ExpertInvitationRepositoryInterface;
use App\Interface\Repository\ExpertRepositoryInterface;
use App\Interface\Service\AuthServiceInterface;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private ExpertRepositoryInterface           $expertRepository,
        private ExpertInvitationRepositoryInterface $invitationRepository,
    ) {}

    public function login(object $data): JsonResponse
    {
        $role = $data->role; // 'admin' or 'expert'

        // Route to the correct model based on the role sent by the client
        $user = match ($role) {
            'admin'  => Admin::where('email', $data->email)->first(),
            'expert' => $this->expertRepository->findByEmail($data->email),
            default  => null,
        };

        // Unified error — do not reveal whether email or password was wrong
        if (! $user || ! Hash::check($data->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        // Experts must still be active
        if ($role === 'expert' && ! $user->is_active) {
            return response()->json(['message' => 'Account is inactive. Contact your administrator.'], 403);
        }

        // Delete any existing tokens for this user to enforce single-session
        $user->tokens()->delete();

        // Issue a new token scoped to the role — ability checked in route middleware
        $token = $user->createToken("{$role}-session", [$role])->plainTextToken;

        return response()->json([
            'token' => $token,
            'role'  => $role,
            'user'  => [
                'id'   => $user->id,
                'name' => $user->name,
            ],
        ], 200);
    }

    public function validateInvitation(object $data): JsonResponse
    {
        $invitation = $this->invitationRepository->findByToken($data->token);

        if (! $invitation || ! $invitation->isValid()) {
            return response()->json([
                'valid' => false,
                'email' => null,
            ], 200);
        }

        return response()->json([
            'valid' => true,
            'email' => $invitation->email,
        ], 200);
    }

    public function registerExpert(object $data): JsonResponse
    {
        // Validate the invitation token — double-checked here (was also checked in FormRequest)
        $invitation = $this->invitationRepository->findByToken($data->token);

        if (! $invitation || ! $invitation->isValid()) {
            return response()->json(['message' => 'Invitation link is invalid or has expired.'], 422);
        }

        // Run both DB writes in one transaction — if expert creation fails,
        // the token is not marked used (atomic operation)
        DB::transaction(function () use ($data, $invitation) {
            $this->expertRepository->create($data);
            $this->invitationRepository->markUsed($data->token);
        });

        return response()->json(['message' => 'Registration successful. Please log in.'], 201);
    }

    public function logout(): JsonResponse
    {
        // Delete only the current token — not all tokens (in case of multi-device)
        auth()->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.'], 200);
    }
}
