<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'title'              => $this->title,
            'description'        => $this->description,
            'status'             => $this->whenNotNull($this->status),      // hidden on public endpoint
            'is_visible'         => $this->whenNotNull($this->is_visible),
            'has_essay_question' => $this->has_essay_question,
            'essay_prompt'       => $this->essay_prompt,
            'questions'          => QuestionResource::collection($this->whenLoaded('questions')),
            'severity_levels'    => SeverityLevelResource::collection($this->whenLoaded('severityLevels')),
            'expert_id'          => $this->whenNotNull($this->expert_id),   // omitted on public resource
            'created_at'         => $this->created_at,
        ];
    }
}
