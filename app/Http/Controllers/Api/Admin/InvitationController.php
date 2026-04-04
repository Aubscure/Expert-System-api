<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvitationRequest;
use App\Interface\Service\AdminServiceInterface;
use Illuminate\Http\JsonResponse;

class InvitationController extends Controller
{
    public function __construct(
        private AdminServiceInterface $adminService,
    ) {}

    // GET /api/admin/invitations
    public function index(): JsonResponse
    {
        return $this->adminService->getAllInvitations();
    }

    // POST /api/admin/invitations
    public function store(StoreInvitationRequest $request): JsonResponse
    {
        return $this->adminService->createInvitation($request->validated('email'));
    }

    // DELETE /api/admin/invitations/{id}
    public function destroy(int $id): JsonResponse
    {
        return $this->adminService->revokeInvitation($id);
    }
}
