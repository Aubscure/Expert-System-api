<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpertResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'email'                => $this->email,
            'is_active'            => $this->is_active,
            'questionnaires_count' => $this->questionnaires_count ?? null,
            'created_at'           => $this->created_at,
        ];
    }
}
