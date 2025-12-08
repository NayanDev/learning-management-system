<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';
    protected $primaryKey = 'id';
    protected $fillable = ["name", "event_id", "user_id", "divisi", "image"];
    protected $appends = ['view_image', 'btn_access', 'btn_delete', 'btn_edit', 'btn_show', 'question'];


    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function answerParticipants()
    {
        return $this->hasMany(AnswerParticipant::class);
    }


    public function getViewImageAttribute()
    {
        if ($this->image) {
            return asset('images/soal/' . $this->image);
        }
        return null;
    }


    public function getQuestionAttribute()
    {
        $data = $this->name;
        $data = strip_tags($data);
        return (string) $data;
    }


    public function getBtnAccessAttribute()
    {
        $html = "<a href='" . url('answer') . "?question_id=" . $this->id . "' class='btn btn-outline-warning btn-sm radius-6' style='margin:1px;'>
                <i class='ti ti-server'></i>
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
