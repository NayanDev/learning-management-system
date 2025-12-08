<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultQuestion extends Model
{
    use HasFactory;

    protected $table = 'result_questions';
    protected $primaryKey = 'id';
    protected $fillable = ["participant_id", "user_id", "type", "pretest_score", "posttest1_score", "posttest2_score"];
    protected $appends = ['pretest', 'posttest', 'btn_multilink', 'btn_delete', 'btn_edit', 'btn_show'];


    public function getPretestAttribute()
    {
        if ($this->pretest_score >= 69) {
            $result = "<span style='color:green;'>" . $this->pretest_score . "</span>";
        } else {
            $result = "<span style='color:red;'>" . $this->pretest_score . "</span>";
        }
        return $result;
    }


    public function getPosttestAttribute()
    {
        $selisihScore = $this->posttest1_score - $this->pretest_score;

        if ($selisihScore > 0) {
            $score = "<span style='color:green;'>" . " (+" . $selisihScore . ")" . "</span>";
        } elseif ($selisihScore < 0) {
            $score = "<span style='color:red;'>" . " (" . $selisihScore . ")" . "</span>";
        } else {
            $score = "<span>" . " (" . $selisihScore . ")" . "</span>";
        }

        if ($this->posttest1_score >= 69) {
            $result = "<span style='color:green;'>" . $this->posttest1_score . $score . "</span>";
        } elseif ($this->posttest1_score === 0 || $this->posttest1_score === null) {
            $result = "<span>" . $this->posttest1_score . $score . "</span>";
        } else {
            $result = "<span style='color:red;'>" . $this->posttest1_score . $score  . "</span>";
        }

        return $result;
    }


    public function getBtnMultilinkAttribute()
    {
        $arrLink = [
            ['label' => 'Report Pre-test', 'url' => url('assesment-pdf') . "?participant_id=" . $this->participant_id . '&type=pre_test', 'icon' => 'ti ti-file'],
            ['label' => 'Report Post-test', 'url' => url('assesment-pdf') . "?participant_id=" . $this->participant_id . '&type=post_test', 'icon' => 'ti ti-file'],
            ['label' => 'Report Post-test 2', 'url' => url('assesment-pdf') . "?participant_id=" . $this->participant_id . '&type=post_test_2', 'icon' => 'ti ti-file'],
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
