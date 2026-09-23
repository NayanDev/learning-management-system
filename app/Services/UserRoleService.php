<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class UserRoleService
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function isDeveloper(): bool
    {
        return $this->user?->role?->name === 'developer';
    }

    public function isSupervisiHR(): bool
    {
        return $this->user?->role?->name === 'supervisi'
            && $this->user?->divisi === 'UMUM & SDM';
    }

    public function isSupervisi(): bool
    {
        return $this->user?->role?->name === 'supervisi';
    }

    public function isManager(): bool
    {
        return $this->user?->role?->name === 'supervisi' && $this->user?->position == 'MANAGER';
    }

    public function isAdministrator(): bool
    {
        return $this->user?->role?->name === 'administrator';
    }

    public function isTrainer(): bool
    {
        return $this->user?->role?->name === 'trainer';
    }

    public function isParticipant(): bool
    {
        return $this->user?->role?->name === 'participant';
    }
}
