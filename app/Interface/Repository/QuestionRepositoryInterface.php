<?php

namespace App\Interface\Repository;

interface QuestionRepositoryInterface
{
    public function getById(int $id);

    public function create(object $data, int $questionnaireId);

    public function update(object $data, int $id);

    public function delete(int $id);

    // Reorder all questions in a questionnaire after a deletion
    public function reorder(int $questionnaireId);
}
