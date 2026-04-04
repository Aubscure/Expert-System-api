<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'body'        => $this->body,
            'order_index' => $this->order_index,
            'choices'     => ChoiceResource::collection($this->whenLoaded('choices')),
        ];
    }
}
