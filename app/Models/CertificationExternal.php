<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificationExternal extends Model
{
    use HasFactory;

    protected $table = 'certification_externals';
    protected $primaryKey = 'id';
    protected $fillable = ["participant_id","event_id","file_path"];
    protected $appends = ['btn_print', 'btn_delete', 'btn_edit', 'btn_show'];


    public function getBtnPrintAttribute()
    {
        $html = "<a href='" . asset('images/certification_external/' . $this->file_path) . "' target='_blank' class='btn btn-outline-success btn-sm radius-6' style='margin:1px;'>
                    <i class='ti ti-printer'></i>
                </a>";

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
