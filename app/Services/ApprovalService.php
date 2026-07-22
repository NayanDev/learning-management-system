<?php

namespace App\Services;

use App\Models\Approval;
use Illuminate\Support\Facades\Auth;

class ApprovalService
{
    public static function render(object $model, array $config): string
    {
        $user = Auth::user();

        if (!$user) return '';

        $roleName = $user->role?->name;

        $approval = Approval::where('approval_type', $config['type'])
            ->where('approval_id', $model->id)
            ->first();

        // =========================
        // BUTTONS
        // =========================

        $btnFile = self::btnFile($config['route_file'], $model->id);
        $btnAction = self::btnAction($model, $approval, $config);
        $btnWaiting = self::btnWaiting();

        // =========================
        // RULES
        // =========================

        if (!$approval) {
            return $btnAction;
        }

        if ($approval->status === 'submit') {
            if (in_array($roleName, $config['can_approve_roles'] ?? [])) {
                return $btnAction;
            }

            return $btnWaiting;
        }

        if ($approval->status === 'approve') {
            return $btnFile;
        }

        return '';
    }

    private static function btnFile(string $route, int $id): string
    {
        $url = url($route) . "?id={$id}";

        return "<a class='btn btn-sm btn-outline-success radius-6'
                    target='_blank'
                    href='{$url}'>
                    <i class='ti ti-file'></i>
                </a>";
    }

    private static function btnAction($model, $approval, array $config): string
    {
        $data = [
            'id'     => $model->id,
            'year'   => $model->year ?? null,
            'status' => $approval?->status,
        ];

        $encoded = htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');

        return "<button type='button'
                    class='btn btn-outline-info btn-sm radius-6'
                    data-bs-toggle='modal'
                    data-bs-target='#modalApproval'
                    onclick='setApproval({$encoded})'>
                    <i class='ti ti-send'></i>
                </button>";
    }

    private static function btnWaiting(): string
    {
        return "<button type='button'
                    class='btn btn-outline-dark btn-sm radius-6'
                    disabled>
                    <i class='ti ti-loader'></i>
                </button>";
    }
}