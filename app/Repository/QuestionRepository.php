<?php

namespace App\Repository;

use App\Interface\Repository\QuestionRepositoryInterface;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class QuestionRepository implements QuestionRepositoryInterface
{
    public function getById(int $id)
    {
        return Question::with(['choices', 'questionnaire:id,expert_id'])
            ->findOrFail($id);
    }

    public function create(object $data, int $questionnaireId)
    {
        // Place new question at the end of the list
        $maxOrder = Question::where('questionnaire_id', $questionnaireId)->max('order_index') ?? 0;

        $question                   = new Question();
        $question->questionnaire_id = $questionnaireId;
        $question->body             = $data->body;
        $question->order_index      = $maxOrder + 1;
        $question->save();

        return $question->fresh();
    }

    public function update(object $data, int $id)
    {
        $question = Question::findOrFail($id);

        if (isset($data->body))        $question->body        = $data->body;
        if (isset($data->order_index)) $question->order_index = $data->order_index;

        $question->save();

        return $question->fresh();
    }

    public function delete(int $id)
    {
        $question = Question::findOrFail($id);
        $question->delete();
    }

    public function reorder(int $questionnaireId)
    {
        // Re-index order_index to be sequential after a deletion
        // Wrapped in a transaction to prevent partial updates
        DB::transaction(function () use ($questionnaireId) {
            $questions = Question::where('questionnaire_id', $questionnaireId)
                ->orderBy('order_index')
                ->select('id', 'order_index')
                ->get();

            foreach ($questions as $index => $question) {
                $question->update(['order_index' => $index + 1]);
            }
        });
    }
}
