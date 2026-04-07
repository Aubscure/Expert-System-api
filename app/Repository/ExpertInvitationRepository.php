<?php

namespace App\Repository;

use App\Interface\Repository\ExpertInvitationRepositoryInterface;
use App\Models\ExpertInvitation;
use Illuminate\Support\Str;

class ExpertInvitationRepository implements ExpertInvitationRepositoryInterface
{
    public function getAll() {
        return ExpertInvitation::paginate(20);
    }

    public function findByToken(string $token)
    {
        return ExpertInvitation::where('token', $token)->first();
    }

    public function create(int $adminId, ?string $email): object
    {
        $invitation             = new ExpertInvitation();
        $invitation->token      = (string) Str::uuid();
        $invitation->email      = $email;
        $invitation->created_by = $adminId;
        $invitation->expires_at = now()->addHours(48);
        $invitation->save();

        return $invitation->fresh();
    }

    public function markUsed(string $token): void
    {
        ExpertInvitation::where('token', $token)
            ->update(['used_at' => now()]);
    }

    public function revoke(int $id): void
    {
        // Only allow revoking unused invitations — used ones are historical records
        ExpertInvitation::where('id', $id)
            ->whereNull('used_at')
            ->delete();
    }
}
