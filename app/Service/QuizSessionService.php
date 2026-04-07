<?php

namespace App\Service;

use App\Http\Resources\QuizResultResource;
use App\Interface\Repository\QuizSessionRepositoryInterface;
use App\Interface\Repository\QuestionnaireRepositoryInterface;
use App\Interface\Repository\SeverityLevelRepositoryInterface;
use App\Interface\Service\QuizSessionServiceInterface;
use App\Service\AiAnalysisService;
use App\Service\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class QuizSessionService implements QuizSessionServiceInterface
{
    public function __construct(
        private QuizSessionRepositoryInterface   $sessionRepository,
        private QuestionnaireRepositoryInterface $questionnaireRepository,
        private SeverityLevelRepositoryInterface $severityLevelRepository,
        private AiAnalysisService                $aiService,
        private PdfService                       $pdfService,
    ) {}

    public function create(int $questionnaireId): JsonResponse
    {
        // Confirm the questionnaire is still public before creating a session
        $this->questionnaireRepository->getPublicById($questionnaireId);

        $session = $this->sessionRepository->create($questionnaireId);

        return response()->json([
            'uuid'           => $session->uuid,
            'started_at'     => $session->started_at,
        ], 201);
    }

    public function submitResponses(string $uuid, array $responses): JsonResponse
    {
        $session = $this->sessionRepository->getByUuid($uuid);

        // Prevent re-submission on an already completed session
        if (! is_null($session->completed_at)) {
            return response()->json(['message' => 'This session has already been completed.'], 409);
        }

        $this->sessionRepository->saveResponses($session->id, $responses);

        return response()->json(['message' => 'Responses saved.'], 200);
    }

    public function complete(string $uuid): JsonResponse
    {
        $session = $this->sessionRepository->getByUuid($uuid);

        if (! is_null($session->completed_at)) {
            return response()->json(['message' => 'This session has already been completed.'], 409);
        }

        // Load all responses with their chosen choices (to sum score_value)
        $session->load(['responses.choice']);

        // ── Score computation ─────────────────────────────────────────────
        $totalScore = $session->responses
            ->filter(fn ($r) => ! is_null($r->choice))      // skip the essay response
            ->sum(fn ($r) => $r->choice->score_value);

        // Match score to the expert-defined severity band
        $severityLevel = $this->severityLevelRepository->findByScore(
            $session->questionnaire_id,
            $totalScore
        );

        if (! $severityLevel) {
            // Edge case: score is out of all defined ranges — still complete the session
            // Service layer logs this; admin can investigate
            \Log::warning('QuizSession UUID=' . $uuid . ' score=' . $totalScore . ' matched no severity level.');
        }

        // ── AI analysis (optional — gracefully degrades if essay was skipped) ──
        $essayText = $session->essay_text;
        $aiAnalysis = null;

        if (! empty($essayText)) {
            $aiAnalysis = $this->aiService->analyze(
                score:         $totalScore,
                maxScore:      $session->responses->filter(fn ($r) => ! is_null($r->choice))->count() * 3, // approximate max
                severityLabel: $severityLevel?->label ?? 'Unknown',
                essayText:     $essayText,
            );

            // Privacy: clear the essay from the session record after AI has consumed it
            $this->sessionRepository->clearEssayText($session->id);
        }

        // ── Finalize the session ──────────────────────────────────────────
        $completed = $this->sessionRepository->complete(
            uuid:            $uuid,
            totalScore:      $totalScore,
            severityLevelId: $severityLevel?->id ?? $session->severity_level_id,
            aiAnalysis:      $aiAnalysis,
        );

        return response()->json(new QuizResultResource($completed));
    }

    public function getResult(string $uuid): JsonResponse
    {
        $session = $this->sessionRepository->getByUuid($uuid);

        if (is_null($session->completed_at)) {
            return response()->json(['message' => 'This session has not been completed yet.'], 404);
        }

        return response()->json(new QuizResultResource($session));
    }

    public function downloadPdf(string $uuid, ?string $displayName): Response
    {
        $session = $this->sessionRepository->getByUuid($uuid);

        if (is_null($session->completed_at)) {
            abort(404, 'Result not found.');
        }

        return $this->pdfService->generate($session, $displayName);
    }

    public function getCompletedForQuestionnaire(int $questionnaireId, int $expertId): JsonResponse
    {
        // Ownership check before exposing any result data
        $questionnaire = $this->questionnaireRepository->getById($questionnaireId);
        if ((int) $questionnaire->expert_id !== $expertId) {
            abort(403, 'You do not have permission to view these results.');
        }

        $sessions = $this->sessionRepository->getCompletedForQuestionnaire($questionnaireId);
        return response()->json($sessions);
    }
}
