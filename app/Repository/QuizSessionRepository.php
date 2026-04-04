<?php

namespace App\Repository;

use App\Interface\Repository\QuizSessionRepositoryInterface;
use App\Models\QuizResponse;
use App\Models\QuizSession;
use Illuminate\Support\Facades\DB;

class QuizSessionRepository implements QuizSessionRepositoryInterface
{
    public function create(int $questionnaireId): object
    {
        // UUID and started_at are auto-set in the model's booted() hook
        $session = new QuizSession();
        $session->questionnaire_id = $questionnaireId;
        $session->save();

        return $session->fresh();
    }

    public function getByUuid(string $uuid)
    {
        return QuizSession::where('uuid', $uuid)
            ->with(['questionnaire:id,title,description', 'severityLevel'])
            ->firstOrFail();
    }

    public function saveResponses(int $sessionId, array $responses): void
    {
        // Bulk insert in a single transaction — avoid N+1 writes
        DB::transaction(function () use ($sessionId, $responses) {
            foreach ($responses as $response) {
                QuizResponse::updateOrCreate(
                    // Unique key: one response per question per session
                    ['quiz_session_id' => $sessionId, 'question_id' => $response['question_id']],
                    [
                        'choice_id'  => $response['choice_id']  ?? null,
                        'essay_text' => $response['essay_text'] ?? null,
                    ]
                );
            }
        });
    }

    public function complete(string $uuid, int $totalScore, int $severityLevelId, ?string $aiAnalysis): object
    {
        $session = QuizSession::where('uuid', $uuid)->firstOrFail();

        DB::transaction(function () use ($session, $totalScore, $severityLevelId, $aiAnalysis) {
            $session->total_score       = $totalScore;
            $session->severity_level_id = $severityLevelId;
            $session->ai_analysis       = $aiAnalysis;
            $session->completed_at      = now();
            $session->save();
        });

        return $session->fresh(['questionnaire', 'severityLevel']);
    }

    public function clearEssayText(int $sessionId): void
    {
        // Privacy: essay text is personal — delete it once AI has consumed it
        QuizResponse::where('quiz_session_id', $sessionId)
            ->whereNotNull('essay_text')
            ->update(['essay_text' => null]);
    }

    public function getCompletedForQuestionnaire(int $questionnaireId)
    {
        // Expert results view — no identity data exposed
        return QuizSession::completed()
            ->where('questionnaire_id', $questionnaireId)
            ->select('uuid', 'total_score', 'severity_level_id', 'completed_at')
            ->with('severityLevel:id,label,color_hex')
            ->orderByDesc('completed_at')
            ->paginate(20);
    }

    public function getStats(): array
    {
        // Admin dashboard stats — all aggregates, no individual data
        return [
            'total_sessions_started'   => QuizSession::count(),
            'total_sessions_completed' => QuizSession::completed()->count(),
        ];
    }
}
