<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TrainingNeed extends Model
{
    use HasFactory;

    protected $table = 'training_needs';
    protected $primaryKey = 'id';
    protected $fillable = ["training_id", "user_id", "divisi", "approve_by", "status", "notes", "created_date"];
    protected $appends = ['btn_access', 'btn_approve', 'btn_delete', 'btn_edit', 'btn_show', 'badge_status'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function training()
    {
        return $this->belongsTo(Training::class, 'training_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approve_by');
    }

    public function workshops()
    {
        return $this->hasMany(TrainingWorkshop::class, 'training_need_id');
    }

    public function participants()
    {
        return $this->hasMany(TrainingParticipant::class, 'training_need_workshop_id');
    }


    public function getBadgeStatusAttribute()
    {
        if ($this->status === "open") {
            $badge = '<span class="badge bg-light-secondary rounded-pill f-12">Open</span>';
        } elseif ($this->status === "submit") {
            $badge = '<span class="badge bg-light-warning rounded-pill f-12">Submit</span>';
        } elseif ($this->status === "approve") {
            $badge = '<span class="badge bg-light-primary rounded-pill f-12">Approve</span>';
        } elseif ($this->status === "close") {
            $badge = '<span class="badge bg-light-success rounded-pill f-12">Close</span>';
        } elseif ($this->status === "reject") {
            $badge = '<span class="badge bg-light-danger rounded-pill f-12">Reject</span>';
        } else {
            $badge = '<span class="badge bg-light-dark rounded-pill f-12">data tidak ditemukan</span>';
        }
        return $badge;
    }


    public function getBtnAccessAttribute()
    {
        $html = "<a href='" . url('training-workshop') . "?training_need=" . $this->id . "' class='btn btn-outline-warning btn-sm radius-6' style='margin:1px;'>
                <i class='ti ti-eye'></i>
                </a>";
        return $html;
    }


    public function getBtnApproveAttribute()
    {
        $data = [
            'id' => $this->id,
            'status' => $this->status,
            'year' => $this->year,
            'notes' => $this->notes,
        ];

        $roleName = Auth::user()->role->name;

        $btn = "<button type='button' class='btn btn-outline-info btn-sm radius-6' style='margin:1px;' 
                data-bs-toggle='modal'  
                data-bs-target='#modalApproval' 
                onclick='setApproval(" . json_encode($data) . ")'>
                <i class='ti ti-send'></i>
            </button>";
        $btnOff = "<button type='button' class='btn btn-outline-dark btn-sm radius-6' style='margin:1px;'>
                <i class='ti ti-loader'></i>
            </button>";
        $pdf = "<a id='export-pdf' class='btn btn-sm btn-outline-success radius-6' target='_blank' href='" . url('training-need-pdf') . "?training_id=" . $this->id . "' title='Export PDF'><i class='ti ti-file'></i></a>";

        if ($this->status === "open" && ($roleName === "staff" || $roleName === "admin")) {
            $html = $btn;
            return $html;
        } else if ($this->status === "submit") {
            if ($roleName === "staff" || $roleName === "admin") {
                $html = $btnOff;
                return $html;
            } else if ($roleName === "manager") {
                $html = $btn;
                return $html;
            }
        } else if ($this->status === "approve") {
            $html = $pdf;
            return $html;
        }
    }


    public function getBtnApprovalAttribute()
    {
        $html = "<button type='button' class='btn btn-outline-info btn-sm radius-6' style='margin:1px;' data-bs-toggle='modal' data-bs-target='#modalAccess' onclick='setAccess(" . json_encode($this->id) . ")'>
                    <i class='ti ti-send'></i>
                </button>";

        return $html;
    }


    public function getBtnDeleteAttribute()
    {
        $html = "<button type='button' class='btn btn-outline-danger btn-sm radius-6' style='margin:1px;' data-bs-toggle='modal' data-bs-target='#modalDelete' onclick='setDelete(" . json_encode($this->id) . ")'>
                    <i class='ti ti-trash'></i>
                </button>";

        return $html;
    }


    public function getBtnEditAttribute()
    {
        $html = "<button type='button' class='btn btn-outline-secondary btn-sm radius-6' style='margin:1px;' data-bs-toggle='offcanvas'  data-bs-target='#drawerEdit' onclick='setEdit(" . json_encode($this->id) . ")'>
                    <i class='ti ti-pencil'></i>
                </button>";

        return $html;
    }


    public function getBtnShowAttribute()
    {
        $html = "<button type='button' class='btn btn-outline-secondary btn-sm radius-6' style='margin:1px;' onclick='setShowPreview(" . json_encode($this->id) . ")'>
                <i class='ti ti-eye'></i>
                </button>";
        return $html;
    }


    public function getUpdatedAtAttribute($value)
    {
        return $value ? date("Y-m-d H:i:s", strtotime($value)) : "-";
    }


    public function getCreatedAtAttribute($value)
    {
        return $value ? date("Y-m-d H:i:s", strtotime($value)) : "-";
    }
}
