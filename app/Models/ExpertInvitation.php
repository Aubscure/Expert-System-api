<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'email',
        'created_by',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // The admin who generated this invitation
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    // Determine if this invitation is still usable
    public function isValid(): bool
    {
        return is_null($this->used_at) && $this->expires_at->isFuture();
    }

    // Scope: only invitations that have not been used and haven't expired
    public function scopePending($query)
    {
        return $query->whereNull('used_at')->where('expires_at', '>', now());
    }

    // Scope: invitations that are expired (unused and past expiry)
    public function scopeExpired($query)
    {
        return $query->whereNull('used_at')->where('expires_at', '<=', now());
    }
}
