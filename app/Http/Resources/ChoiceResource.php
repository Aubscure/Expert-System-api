<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChoiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'body'        => $this->body,
            'order_index' => $this->order_index,
            // score_value is intentionally excluded from the public API response
            // Experts see it in their dashboard; public respondents never do
        ];
    }
}
