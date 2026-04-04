<?php

namespace App\Http\Controllers\Api\Expert;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expert\StoreChoiceRequest;
use App\Interface\Service\ChoiceServiceInterface;
use Illuminate\Http\JsonResponse;

class ChoiceController extends Controller
{
    public function __construct(
        private ChoiceServiceInterface $choiceService,
    ) {}

    // POST /api/expert/questions/{questionId}/choices
    public function store(StoreChoiceRequest $request, int $questionId): JsonResponse
    {
        return $this->choiceService->create($request, $questionId, auth()->id());
    }

    // PUT /api/expert/choices/{id}
    public function update(StoreChoiceRequest $request, int $id): JsonResponse
    {
        return $this->choiceService->update($request, $id, auth()->id());
    }

    // DELETE /api/expert/choices/{id}
    public function destroy(int $id): JsonResponse
    {
        return $this->choiceService->delete($id, auth()->id());
    }
}
