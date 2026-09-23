<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RoleAccessService
{
    public function apply(Builder $query, ?User $user = null): Builder
    {
        $user ??= Auth::user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        switch ($user->role) {
            case 'developer':
                // Full access
                break;

            case 'supervisi':
                if ($this->isUmumSdm($user)) {
                    // Full access
                } else {
                    $query->where(
                        'employees.division_id',
                        $user->division_id
                    );
                }
                break;

            case 'administrator':
            case 'trainer':
            case 'participant':
                $query->where(
                    'employees.id',
                    $user->employee_id
                );
                break;

            default:
                // Role tidak dikenal = tidak boleh melihat data
                $query->whereRaw('1 = 0');
                break;
        }

        return $query;
    }

    protected function isUmumSdm(User $user): bool
    {
        return $user->division?->name === 'Umum & SDM';
    }
}
