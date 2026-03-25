<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Silabus extends Model
{
    protected $table = 'silabuses';
    protected $primaryKey = 'id';
    protected $fillable = ["code_module", "duration", "description", "target", "qualification", "materi_id", "workshop_id", "pic", "grading", "user", "manager", "director"];
    protected $appends = ['btn_delete', 'btn_edit', 'btn_show'];
}
