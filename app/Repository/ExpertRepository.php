<?php

namespace App\Repository;

use App\Interface\Repository\ExpertRepositoryInterface;
use App\Models\Expert;
use Illuminate\Support\Facades\Hash;

class ExpertRepository implements ExpertRepositoryInterface
{
    public function getAll()
    {
        return Expert::select('id', 'name', 'email', 'is_active', 'created_at')
            ->withCount('questionnaires')
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    public function getById(int $id)
    {
        return Expert::select('id', 'name', 'email', 'is_active', 'created_at')
            ->findOrFail($id);
    }

    public function findByEmail(string $email)
    {
        return Expert::where('email', $email)->first();
    }

    public function create(object $data): object
    {
        $expert           = new Expert();
        $expert->name     = $data->name;
        $expert->email    = $data->email;
        $expert->password = Hash::make($data->password);
        $expert->save();

        return $expert->fresh();
    }

    public function deactivate(int $id): void
    {
        $expert = Expert::findOrFail($id);

        // Revoke all active Sanctum tokens immediately — invalidates active sessions
        $expert->tokens()->delete();

        // Mark as inactive and soft delete so the record is preserved for audit
        $expert->is_active = false;
        $expert->save();
        $expert->delete();
    }
}
