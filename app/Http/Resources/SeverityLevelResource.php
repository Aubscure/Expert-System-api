<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeverityLevelResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'label'       => $this->label,
            'min_score'   => $this->min_score,
            'max_score'   => $this->max_score,
            'description' => $this->description,
            'color_hex'   => $this->color_hex,
            'order_index' => $this->order_index,
        ];
    }
}
