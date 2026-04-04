<?php

namespace App\Repository;

use App\Interface\Repository\SeverityLevelRepositoryInterface;
use App\Models\SeverityLevel;

class SeverityLevelRepository implements SeverityLevelRepositoryInterface
{
    public function getAllForQuestionnaire(int $questionnaireId)
    {
        return SeverityLevel::where('questionnaire_id', $questionnaireId)
            ->select('id', 'questionnaire_id', 'label', 'min_score', 'max_score', 'description', 'color_hex', 'order_index')
            ->orderBy('min_score')
            ->get();
    }

    public function getById(int $id)
    {
        return SeverityLevel::with('questionnaire:id,expert_id')
            ->findOrFail($id);
    }

    public function create(object $data, int $questionnaireId)
    {
        $maxOrder = SeverityLevel::where('questionnaire_id', $questionnaireId)->max('order_index') ?? 0;

        $level                     = new SeverityLevel();
        $level->questionnaire_id   = $questionnaireId;
        $level->label              = $data->label;
        $level->min_score          = $data->min_score;
        $level->max_score          = $data->max_score;
        $level->description        = $data->description ?? null;
        $level->color_hex          = $data->color_hex ?? null;
        $level->order_index        = $maxOrder + 1;
        $level->save();

        return $level->fresh();
    }

    public function update(object $data, int $id)
    {
        $level = SeverityLevel::findOrFail($id);

        if (isset($data->label))       $level->label       = $data->label;
        if (isset($data->min_score))   $level->min_score   = $data->min_score;
        if (isset($data->max_score))   $level->max_score   = $data->max_score;
        if (isset($data->description)) $level->description = $data->description;
        if (isset($data->color_hex))   $level->color_hex   = $data->color_hex;

        $level->save();

        return $level->fresh();
    }

    public function delete(int $id)
    {
        $level = SeverityLevel::findOrFail($id);
        $level->delete();
    }

    public function findByScore(int $questionnaireId, int $score)
    {
        // Find the severity band that contains the given score
        return SeverityLevel::where('questionnaire_id', $questionnaireId)
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first(); // returns null if no level matches (edge case: score out of range)
    }
}
