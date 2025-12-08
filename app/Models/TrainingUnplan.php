<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TrainingUnplan extends Model
{
    use HasFactory;

    protected $table = 'training_unplanes';
    protected $primaryKey = 'id';
    protected $fillable = ["training_id", "user_id", "workshop_id", "organizer", "speaker", "start_date", "end_date", "divisi", "instructor", "location", "status", "notes", "approve_by"];
    protected $appends = ['btn_approve', 'btn_access', 'btn_delete', 'btn_edit', 'btn_show', 'badge_status'];


    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'workshop_id');
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
        $html = "<a href='" . url('training-unplan-participant') . "?training_unplan=" . $this->id . "' class='btn btn-outline-warning btn-sm radius-6' style='margin:1px;'>
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

        if ($this->status === "open") {
            $html = $btn;
            return $html;
        } else if ($this->status === "submit") {
            if ($roleName === "admin") {
                $html = $btn;
                return $html;
            } else {
                $html = $btnOff;
                return $html;
            }
        }
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
