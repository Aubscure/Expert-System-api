<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionnaire extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expert_id',
        'title',
        'description',
        'status',
        'is_visible',
        'has_essay_question',
        'essay_prompt',
    ];

    protected $casts = [
        'is_visible'         => 'boolean',
        'has_essay_question' => 'boolean',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
        'deleted_at'         => 'datetime',
    ];

    // The expert who owns this questionnaire
    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }

    // Questions belonging to this questionnaire, ordered for display
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order_index');
    }

    // Severity levels defined by the expert for score interpretation
    public function severityLevels()
    {
        return $this->hasMany(SeverityLevel::class)->orderBy('min_score');
    }

    // All quiz sessions taken on this questionnaire
    public function quizSessions()
    {
        return $this->hasMany(QuizSession::class);
    }

    // Scope: questionnaires available to the public
    public function scopePubliclyVisible($query)
    {
        return $query->where('status', 'published')->where('is_visible', true);
    }

    // Scope: filter to a specific expert's questionnaires
    public function scopeForExpert($query, int $expertId)
    {
        return $query->where('expert_id', $expertId);
    }
}
