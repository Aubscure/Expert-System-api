<?php

namespace App\Http\Controllers\Api\Expert;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expert\StoreSeverityLevelRequest;
use App\Interface\Service\SeverityLevelServiceInterface;
use Illuminate\Http\JsonResponse;

class SeverityLevelController extends Controller
{
    public function __construct(
        private SeverityLevelServiceInterface $severityLevelService,
    ) {}

    // GET /api/expert/questionnaires/{questionnaireId}/severity-levels
    public function index(int $questionnaireId): JsonResponse
    {
        return $this->severityLevelService->getAllForQuestionnaire($questionnaireId);
    }

    // POST /api/expert/questionnaires/{questionnaireId}/severity-levels
    public function store(StoreSeverityLevelRequest $request, int $questionnaireId): JsonResponse
    {
        return $this->severityLevelService->create($request, $questionnaireId, auth()->id());
    }

    // PUT /api/expert/severity-levels/{id}
    public function update(StoreSeverityLevelRequest $request, int $id): JsonResponse
    {
        return $this->severityLevelService->update($request, $id, auth()->id());
    }

    // DELETE /api/expert/severity-levels/{id}
    public function destroy(int $id): JsonResponse
    {
        return $this->severityLevelService->delete($id, auth()->id());
    }
}
