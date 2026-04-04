<?php

namespace App\Service;

use App\Http\Resources\ExpertResource;
use App\Http\Resources\InvitationResource;
use App\Interface\Repository\ExpertInvitationRepositoryInterface;
use App\Interface\Repository\ExpertRepositoryInterface;
use App\Interface\Repository\QuizSessionRepositoryInterface;
use App\Interface\Repository\QuestionnaireRepositoryInterface;
use App\Interface\Service\AdminServiceInterface;
use App\Models\SeverityLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminService implements AdminServiceInterface
{
    public function __construct(
        private ExpertRepositoryInterface           $expertRepository,
        private ExpertInvitationRepositoryInterface $invitationRepository,
        private QuizSessionRepositoryInterface      $sessionRepository,
        private QuestionnaireRepositoryInterface    $questionnaireRepository,
    ) {}

    public function getStats(): JsonResponse
    {
        $sessionStats = $this->sessionRepository->getStats();

        // Severity distribution for the dashboard chart — no individual data
        $distribution = SeverityLevel::select(
                'severity_levels.label',
                'severity_levels.color_hex',
                DB::raw('COUNT(quiz_sessions.id) as count')
            )
            ->leftJoin('quiz_sessions', 'severity_levels.id', '=', 'quiz_sessions.severity_level_id')
            ->groupBy('severity_levels.id', 'severity_levels.label', 'severity_levels.color_hex')
            ->orderByDesc('count')
            ->get();

        return response()->json([
            'total_sessions_started'   => $sessionStats['total_sessions_started'],
            'total_sessions_completed' => $sessionStats['total_sessions_completed'],
            'total_registered_experts' => \App\Models\Expert::withTrashed()->count(),
            'active_experts'           => \App\Models\Expert::active()->count(),
            'severity_distribution'    => $distribution,
        ]);
    }

    public function getAllExperts(): JsonResponse
    {
        $experts = $this->expertRepository->getAll();
        return response()->json(ExpertResource::collection($experts));
    }

    public function deactivateExpert(int $id): JsonResponse
    {
        $this->expertRepository->deactivate($id);
        return response()->json(['message' => 'Expert has been deactivated and their sessions revoked.']);
    }

    public function getAllInvitations(): JsonResponse
    {
        $invitations = $this->invitationRepository->getAll();
        return response()->json(InvitationResource::collection($invitations));
    }

    public function createInvitation(?string $email): JsonResponse
    {
        $adminId = auth()->id();

        $invitation = $this->invitationRepository->create($adminId, $email);

        // Build the full registration URL — frontend URL from env
        $registrationUrl = rtrim(config('app.frontend_url'), '/') . '/register?token=' . $invitation->token;

        return response()->json([
            'invitation'       => new InvitationResource($invitation),
            'registration_url' => $registrationUrl,
        ], 201);
    }

    public function revokeInvitation(int $id): JsonResponse
    {
        $this->invitationRepository->revoke($id);
        return response()->json(['message' => 'Invitation revoked.']);
    }
}
