<?php

namespace App\Repository;

use App\Interface\Repository\QuestionnaireRepositoryInterface;
use App\Models\Questionnaire;
use Illuminate\Support\Facades\DB;

class QuestionnaireRepository implements QuestionnaireRepositoryInterface
{
    public function getAllPublic()
    {
        // Whitelist only the columns the public listing needs
        return Questionnaire::publiclyVisible()
            ->select('id', 'title', 'description', 'has_essay_question', 'created_at')
            ->withCount('questions') // appends questions_count for "X questions" display
            ->orderByDesc('created_at')
            ->paginate(12);
    }

    public function getPublicById(int $id)
    {
        // Load the full structure needed for quiz-taking
        return Questionnaire::publiclyVisible()
            ->with([
                'questions' => fn ($q) => $q->select('id', 'questionnaire_id', 'body', 'order_index'),
                'questions.choices' => fn ($q) => $q->select('id', 'question_id', 'body', 'order_index'),
                // score_value is intentionally excluded from public response
                // the frontend never sees point values — prevents gaming
            ])
            ->select('id', 'title', 'description', 'has_essay_question', 'essay_prompt')
            ->findOrFail($id);
    }

    public function getAllForExpert(int $expertId)
    {
        return Questionnaire::forExpert($expertId)
            ->select('id', 'title', 'status', 'is_visible', 'has_essay_question', 'created_at', 'updated_at')
            ->withCount(['questions', 'quizSessions', 'quizSessions as completed_sessions_count' => fn ($q) => $q->completed()])
            ->orderByDesc('updated_at')
            ->paginate(15);
    }

    public function getById(int $id)
    {
        // Full detail for expert editing — no public scope applied
        return Questionnaire::with([
                'questions' => fn ($q) => $q->select('id', 'questionnaire_id', 'body', 'order_index'),
                'questions.choices',
                'severityLevels',
            ])
            ->select('id', 'expert_id', 'title', 'description', 'status', 'is_visible', 'has_essay_question', 'essay_prompt')
            ->findOrFail($id);
    }

    public function create(object $data, int $expertId)
    {
        $questionnaire              = new Questionnaire();
        $questionnaire->expert_id   = $expertId;
        $questionnaire->title       = $data->title;
        $questionnaire->description = $data->description ?? null;
        $questionnaire->status      = 'draft';
        $questionnaire->is_visible  = false;

        // Essay is optional per questionnaire
        $questionnaire->has_essay_question = $data->has_essay_question ?? true;
        $questionnaire->essay_prompt       = $data->essay_prompt ?? null;
        $questionnaire->save();

        return $questionnaire->fresh();
    }

    public function update(object $data, int $id)
    {
        $questionnaire = Questionnaire::findOrFail($id);

        // Only update fields that were sent (partial update support)
        if (isset($data->title))              $questionnaire->title              = $data->title;
        if (isset($data->description))        $questionnaire->description        = $data->description;
        if (isset($data->has_essay_question)) $questionnaire->has_essay_question = $data->has_essay_question;
        if (isset($data->essay_prompt))       $questionnaire->essay_prompt       = $data->essay_prompt;

        // Status and visibility are controlled by dedicated endpoints, not here
        $questionnaire->save();

        return $questionnaire->fresh();
    }

    public function publish(int $id)
    {
        $questionnaire         = Questionnaire::findOrFail($id);
        $questionnaire->status = 'published';
        $questionnaire->save();

        return $questionnaire->fresh();
    }

    public function toggleVisibility(int $id, bool $isVisible)
    {
        $questionnaire             = Questionnaire::findOrFail($id);
        $questionnaire->is_visible = $isVisible;
        $questionnaire->save();

        return $questionnaire->fresh();
    }

    public function delete(int $id)
    {
        $questionnaire = Questionnaire::findOrFail($id);
        $questionnaire->delete(); // soft delete
    }
}
