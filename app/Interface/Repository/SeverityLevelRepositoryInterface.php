<?php

namespace App\Interface\Repository;

interface SeverityLevelRepositoryInterface
{
    public function getAllForQuestionnaire(int $questionnaireId);

    public function getById(int $id);

    public function create(object $data, int $questionnaireId);

    public function update(object $data, int $id);

    public function delete(int $id);

    // Find the severity level matching a given score
    public function findByScore(int $questionnaireId, int $score);
}
