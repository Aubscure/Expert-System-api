<?php

namespace App\Interface\Repository;

interface ExpertInvitationRepositoryInterface
{
    public function getAll();

    public function findByToken(string $token);

    public function create(int $adminId, ?string $email): object;

    public function markUsed(string $token): void;

    public function revoke(int $id): void;
}
