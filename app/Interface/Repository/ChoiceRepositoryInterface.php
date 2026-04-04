<?php

namespace App\Interface\Repository;

interface ChoiceRepositoryInterface
{
    public function getById(int $id);

    public function create(object $data, int $questionId);

    public function update(object $data, int $id);

    public function delete(int $id);
}
