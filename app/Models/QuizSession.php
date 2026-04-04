<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QuizSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'questionnaire_id',
        'started_at',
        'completed_at',
        'total_score',
        'severity_level_id',
        'ai_analysis',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
        'total_score'  => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    // Auto-generate UUID on creation so the caller never has to
    protected static function booted(): void
    {
        static::creating(function (QuizSession $session) {
            $session->uuid       = (string) Str::uuid();
            $session->started_at = now();
        });
    }

    // Eager-load these whenever a session is fetched for a result page
    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function severityLevel()
    {
        return $this->belongsTo(SeverityLevel::class);
    }

    public function responses()
    {
        return $this->hasMany(QuizResponse::class);
    }

    // Scope: only completed sessions
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }
}
