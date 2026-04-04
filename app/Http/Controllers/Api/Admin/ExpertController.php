<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Interface\Service\AdminServiceInterface;
use Illuminate\Http\JsonResponse;

class ExpertController extends Controller
{
    public function __construct(
        private AdminServiceInterface $adminService,
    ) {}

    // GET /api/admin/experts
    public function index(): JsonResponse
    {
        return $this->adminService->getAllExperts();
    }

    // DELETE /api/admin/experts/{id}
    public function destroy(int $id): JsonResponse
    {
        return $this->adminService->deactivateExpert($id);
    }
}
