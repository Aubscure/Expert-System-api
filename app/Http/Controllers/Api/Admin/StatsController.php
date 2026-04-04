<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Interface\Service\AdminServiceInterface;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function __construct(
        private AdminServiceInterface $adminService,
    ) {}

    // GET /api/admin/stats
    public function index(): JsonResponse
    {
        return $this->adminService->getStats();
    }
}
