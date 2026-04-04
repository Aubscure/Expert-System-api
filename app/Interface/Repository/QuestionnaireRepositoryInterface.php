<?php

namespace App\Interface\Repository;

interface QuestionnaireRepositoryInterface
{
    // Public: only published + visible questionnaires
    public function getAllPublic();

    // Public: get one questionnaire with questions and choices
    public function getPublicById(int $id);

    // Expert: list only their own questionnaires (all statuses)
    public function getAllForExpert(int $expertId);

    // Expert: get a single questionnaire — ownership verified at service layer
    public function getById(int $id);

    public function create(object $data, int $expertId);

    public function update(object $data, int $id);

    // Publish: validates questionnaire structure before flipping status
    public function publish(int $id);

    public function toggleVisibility(int $id, bool $isVisible);

    public function delete(int $id);
}
