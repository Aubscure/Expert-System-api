<?php

namespace App\Repository;

use App\Interface\Repository\ChoiceRepositoryInterface;
use App\Models\Choice;

class ChoiceRepository implements ChoiceRepositoryInterface
{
    public function getById(int $id)
    {
        // Load question->questionnaire for ownership validation upstream
        return Choice::with(['question.questionnaire:id,expert_id'])
            ->findOrFail($id);
    }

    public function create(object $data, int $questionId)
    {
        $maxOrder = Choice::where('question_id', $questionId)->max('order_index') ?? 0;

        $choice              = new Choice();
        $choice->question_id = $questionId;
        $choice->body        = $data->body;
        $choice->score_value = $data->score_value;
        $choice->order_index = $maxOrder + 1;
        $choice->save();

        return $choice->fresh();
    }

    public function update(object $data, int $id)
    {
        $choice = Choice::findOrFail($id);

        if (isset($data->body))        $choice->body        = $data->body;
        if (isset($data->score_value)) $choice->score_value = $data->score_value;
        if (isset($data->order_index)) $choice->order_index = $data->order_index;

        $choice->save();

        return $choice->fresh();
    }

    public function delete(int $id)
    {
        $choice = Choice::findOrFail($id);
        $choice->delete();
    }
}
