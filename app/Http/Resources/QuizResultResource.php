<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResultResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'uuid'           => $this->uuid,
            'total_score'    => $this->total_score,
            'completed_at'   => $this->completed_at,
            'ai_analysis'    => $this->ai_analysis,         // null if essay was skipped
            'severity_level' => new SeverityLevelResource($this->whenLoaded('severityLevel')),
            'questionnaire'  => [
                'id'    => $this->questionnaire->id,
                'title' => $this->questionnaire->title,
            ],
        ];
    }
}
