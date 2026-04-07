<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnairePublicListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                        => $this->id,
            'title'                     => $this->title,
            'description'               => $this->description,
            'has_essay_question'        => $this->has_essay_question,
            'questions_count'           => $this->questions_count,
            'created_at'                => $this->created_at,
        ];
    }
}
