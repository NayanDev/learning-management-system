<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationMonth extends Model
{
    protected $table = 'evaluation_months';
    protected $primaryKey = 'id';
    protected $fillable = ["name","user_id","value","category","event_id","trainer_id","participant_id"];
    protected $appends = ['btn_delete', 'btn_edit', 'btn_show'];
    
    /**
     * Get category based on value
     * 0-5 = D, 6-10 = C, 11-15 = B, 16-20 = A
     */
    public static function calculateCategory($value)
    {
        if ($value >= 0 && $value <= 5) {
            return 'D';
        } elseif ($value >= 6 && $value <= 10) {
            return 'C';
        } elseif ($value >= 11 && $value <= 15) {
            return 'B';
        } elseif ($value >= 16 && $value <= 20) {
            return 'A';
        }
        return null;
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
}
