<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'email'      => $this->email,
            'token'      => $this->token,
            'status'     => $this->used_at   ? 'used'
                          : ($this->expires_at->isPast() ? 'expired' : 'pending'),
            'expires_at' => $this->expires_at,
            'used_at'    => $this->used_at,
            'created_by' => $this->creator?->name,
            'created_at' => $this->created_at,
        ];
    }
}
