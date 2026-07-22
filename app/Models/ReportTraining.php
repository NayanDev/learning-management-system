<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ReportTraining extends Model
{
    use HasFactory;

    protected $table = 'report_trainings';
    protected $primaryKey = 'id';
    protected $fillable = ["event_id","category_training","description","materi","targets","notes","report_date","manager","director","director_name","director_signature"];
    protected $appends = ['btn_signature', 'btn_pdf', 'btn_delete', 'btn_edit', 'btn_show'];


    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function managerUser()
    {
        return $this->belongsTo(User::class, 'manager');
    }

    public function getBtnSignatureAttribute()
    {

        $data = [
            'id' => $this->id,
            'status' => $this->status,
            'year' => $this->year,
            'notes' => $this->notes,
        ];

        $roleName = Auth::user()->role->name;
        $divisiName = Auth::user()->divisi;

        $btn = "<button type='button' class='btn btn-outline-info btn-sm radius-6' style='margin:1px;' 
                data-bs-toggle='modal'  
                data-bs-target='#modalApproval' 
                onclick='setApproval(" . json_encode($data) . ")'>
                <i class='ti ti-send'></i>
            </button>";
        $btnDirector = "<button type='button' class='btn btn-outline-info btn-sm radius-6' style='margin:1px;' 
                data-bs-toggle='modal'  
                data-bs-target='#modalDirectorSignature' 
                onclick='setDirectorSignature(" . json_encode($data) . ")'>
                <i class='ti ti-send'></i>
            </button>";
        $btnOff = "<button type='button' class='btn btn-outline-dark btn-sm radius-6' style='margin:1px;'>
                <i class='ti ti-loader'></i>
            </button>";
        $pdf = "";

        if (($this->status === "open" || $this->status === "reject") && $roleName === "admin") {
            $html = $btn;
            return $html;
        } else if ($this->status === "submit") {
            if ($roleName === "admin") {
                $html = $btnOff;
                return $html;
            } else if ($roleName === "manager" && $divisiName === "UMUM & SDM") {
                $html = $btn;
                return $html;
            }
        } else if ($this->status === "approve") {
            if ($roleName === "admin") {
                $html = $btnDirector;
                return $html;
            } else if ($roleName === "manager" && $divisiName === "UMUM & SDM") {
                $html = $btnOff;
                return $html;
            }
        } else if (($this->status === "close" && $roleName === "admin") || ($this->status === "close" && $roleName === "manager" && $divisiName === "UMUM & SDM") || ($this->status === "close" && $roleName === "direktur")) {
            $html = $pdf;
            return $html;
        }
    }

    public function getBtnDeleteAttribute()
    {
        $html = "<button type='button' class='btn btn-outline-danger btn-sm radius-6' style='margin:1px;' data-bs-toggle='modal' data-bs-target='#modalDelete' onclick='setDelete(" . json_encode($this->id) . ")'>
                    <i class='ti ti-trash'></i>
                </button>";

        return $html;
    }

    public function getBtnPdfAttribute()
    {
        $html = "<a href='" . url('report-training') . "/" . $this->id . "/pdf" . "' class='btn btn-outline-warning btn-sm radius-6' style='margin:1px;'>
                <i class='ti ti-eye'></i>
                </a>";
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
