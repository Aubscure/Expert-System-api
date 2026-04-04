<?php

namespace App\Interface\Repository;

interface ExpertRepositoryInterface
{
    public function getAll();

    public function getById(int $id);

    public function findByEmail(string $email);

    public function create(object $data): object;

    // Deactivate expert and revoke all their Sanctum tokens
    public function deactivate(int $id): void;
}
