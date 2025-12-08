<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $fillable = ["workshop_id", "user_id", "divisi", "year", "letter_number", "organizer", "start_date", "end_date", "token", "token_expired", "instructor", "location", "approve_by", "created_date", "notes", "status"];
    protected $appends = ['btn_delete', 'btn_edit', 'btn_multilink'];


    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'workshop_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approve_by');
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function signpresent()
    {
        return $this->belongsTo(User::class, 'sign_present');
    }

    public function signready()
    {
        return $this->belongsTo(User::class, 'sign_ready');
    }

    public function trainers()
    {
        return $this->hasMany(Trainer::class);
    }


    public function getBtnMultilinkAttribute()
    {
        $arrLink = [
            ['label' => 'Trainer', 'url' => url('trainer') . "?event_id=" . $this->id, 'icon' => 'ti ti-users'],
            ['label' => 'Participant', 'url' => url('participant') . "?event_id=" . $this->id, 'icon' => 'ti ti-users'],
            ['label' => 'Attendance', 'url' => url('attendance') . "?event_id=" . $this->id, 'icon' => 'ti ti-check'],
            ['label' => 'Materi', 'url' => url('materi') . "?event_id=" . $this->id, 'icon' => 'ti ti-book fw-bold'],
            ['label' => 'Question', 'url' => url('question') . "?event_id=" . $this->id, 'icon' => 'ti ti-question-mark fw-bold'],
            ['label' => 'Scoreboard', 'url' => url('scoreboard') . "?event_id=" . $this->id, 'icon' => 'ti ti-award fw-bold'],
            ['label' => 'Question Access', 'url' => route('set.question', ['id' => $this->id]), 'icon' => 'ti ti-qrcode'],
            ['label' => 'Result Question', 'url' => url('result-question') . "?event_id=" . $this->id, 'icon' => 'ti ti-star fw-bold'],
            ['label' => 'Participant Answer', 'url' => url('answer-participant') . "?event_id=" . $this->id, 'icon' => 'ti ti-archive fw-bold'],
            ['label' => 'Evaluation', 'url' => url('evaluation') . "?event_id=" . $this->id, 'icon' => 'ti ti-pencil'],
            ['label' => 'Certification', 'url' => url('certification') . "?event_id=" . $this->id, 'icon' => 'ti ti-certificate'],
            ['label' => 'Documentation', 'url' => url('documentation') . "?event_id=" . $this->id, 'icon' => 'ti ti-photo'],
            ['label' => 'Training Report', 'url' => url('training-report') . "?event_id=" . $this->id, 'icon' => 'ti ti-clipboard'],
        ];
        $html = "<button type='button' data-links='" . json_encode($arrLink) . "' onclick='setMM(this)' title='Navigation' class='btn btn-outline-warning btn-sm radius-6' style='margin:1px;' data-bs-toggle='modal' data-bs-target='#modalMultiLink'>
                    <i class='ti ti-list'></i>
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
