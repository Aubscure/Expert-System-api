<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\SubmitResponsesRequest;
use App\Interface\Service\QuizSessionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class QuizSessionController extends Controller
{
    public function __construct(
        private QuizSessionServiceInterface $sessionService,
    ) {}

    // POST /api/sessions
    public function store(Request $request): JsonResponse
    {
        // questionnaire_id is required to start a session
        $request->validate(['questionnaire_id' => 'required|integer|exists:questionnaires,id']);

        return $this->sessionService->create($request->questionnaire_id);
    }

    // POST /api/sessions/{uuid}/responses
    public function submitResponses(SubmitResponsesRequest $request, string $uuid): JsonResponse
    {
        // Validate UUID format before hitting the database
        if (! Str::isUuid($uuid)) {
            abort(404);
        }

        return $this->sessionService->submitResponses($uuid, $request->validated('responses'));
    }

    // POST /api/sessions/{uuid}/complete
    public function complete(string $uuid): JsonResponse
    {
        if (! Str::isUuid($uuid)) {
            abort(404);
        }

        return $this->sessionService->complete($uuid);
    }

    // GET /api/sessions/{uuid}/result
    public function result(string $uuid): JsonResponse
    {
        if (! Str::isUuid($uuid)) {
            abort(404);
        }

        return $this->sessionService->getResult($uuid);
    }

    // GET /api/sessions/{uuid}/pdf?name=Optional+Name
    public function downloadPdf(Request $request, string $uuid): Response
    {
        if (! Str::isUuid($uuid)) {
            abort(404);
        }

        // Name comes from query string — never stored
        $displayName = $request->query('name');

        return $this->sessionService->downloadPdf($uuid, $displayName);
    }
}
