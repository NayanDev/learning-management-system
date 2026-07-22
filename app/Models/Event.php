<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $fillable = ["workshop_id", "user_id", "divisi", "year", "letter_number", "organizer", "start_date", "end_date", "token", "token_expired", "instructor", "location", "approve_by", "created_date", "notes", "status","command_attachment"];
    protected $appends = ['btn_approve', 'btn_delete', 'btn_edit', 'btn_multilink'];


    public function documentations()
    {
        return $this->hasMany(Documentation::class);
    }

    public function materials()
    {
        return $this->hasMany(Materi::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
    

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


    public function getBtnApproveAttribute()
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
        $pdf = "<a id='export-pdf' class='btn btn-sm btn-outline-success radius-6' target='_blank' href='" . url('attendance-ready-pdf') . "?event_id=" . $this->id . "' title='Export PDF'><i class='ti ti-file'></i></a>";

        if ($this->instructor === "external") {

            // OPEN / REJECT
            if ($this->status === "open" || $this->status === "reject") {
                if ($roleName === "admin") {
                    return $btn;
                }
            }

            // SUBMIT → hanya admin lihat / control
            if ($this->status === "submit") {
                if ($roleName === "admin") {
                    return $btnOff;
                }

                if ($roleName === "manager" && $divisiName === "UMUM & SDM") {
                    return $btn; // approve manager
                }
            }

            // APPROVE → manager sudah selesai (FINISH)
            if ($this->status === "approve") {
                if ($roleName === "manager" && $divisiName === "UMUM & SDM") {
                    return $pdf; // langsung selesai
                }

                if ($roleName === "admin") {
                    return $pdf;
                }
            }

            // CLOSE (optional fallback)
            if ($this->status === "close") {
                return $pdf;
            }

        } else {

            // ✔ INTERNAL: flow normal sampai direktur

            if (
                ($this->status === "open" || $this->status === "reject") &&
                $roleName === "admin"
            ) {
                return $btn;
            }

            if ($this->status === "submit") {
                if ($roleName === "admin") {
                    return $btnOff;
                } elseif ($roleName === "manager" && $divisiName === "UMUM & SDM") {
                    return $btn;
                }
            }

            if ($this->status === "approve") {
                if ($roleName === "admin") {
                    return $btnDirector;
                } elseif ($roleName === "manager" && $divisiName === "UMUM & SDM") {
                    return $btnOff;
                }
            }

            if (
                $this->status === "close" &&
                (
                    $roleName === "admin" ||
                    ($roleName === "manager" && $divisiName === "UMUM & SDM") ||
                    $roleName === "direktur"
                )
            ) {
                return $pdf;
            }
        }
    }


    public function getBtnMultilinkAttribute()
    {
        $token = Event::find($this->id)->token;
        $user = Auth::user();

        $showEvaluationMonth = DB::table('evaluation_months')
            ->where('event_id', $this->id)
            ->where('user_id', Auth::id())
            ->exists();

        $trainers = DB::table('trainers')
            ->where('event_id', $this->id)
            ->where('user_id', Auth::id())
            ->exists();
            
        //CONDITIONAL MENU
        $isAdmin = in_array(optional($user->role)->name, ['admin', 'adminhr']);

        $arrLink = [
            ['label' => 'Trainer', 'url' => url('trainer') . "?event_id=" . $this->id, 'icon' => 'ti ti-users'],
            ['label' => 'Participant', 'url' => url('participant') . "?event_id=" . $this->id, 'icon' => 'ti ti-users'],
            ['label' => 'Attendance', 'url' => url('attendance') . "?event_id=" . $this->id, 'icon' => 'ti ti-check'],
            ['label' => 'Materi', 'url' => url('materi') . "?event_id=" . $this->id, 'icon' => 'ti ti-book fw-bold'],
            ['label' => 'Question', 'url' => url('question') . "?event_id=" . $this->id, 'icon' => 'ti ti-question-mark fw-bold'],
            // ['label' => 'Question Access', 'url' => route('set.question', ['id' => $this->id]), 'icon' => 'ti ti-qrcode'],
            ['label' => 'Result Question', 'url' => url('result-question') . "?event_id=" . $this->id, 'icon' => 'ti ti-star fw-bold'],
            // ['label' => 'Participant Answer', 'url' => url('answer-participant') . "?event_id=" . $this->id, 'icon' => 'ti ti-archive fw-bold'],
            // ['label' => 'Evaluation', 'url' => url('evaluation') . "?event_id=" . $this->id, 'icon' => 'ti ti-pencil'],
            // ['label' => 'Certification', 'url' => url('certification') . "?event_id=" . $this->id, 'icon' => 'ti ti-certificate'],
            // ['label' => 'Checkout Access', 'url' => route('set.checkout', ['id' => $this->id]), 'icon' => 'ti ti-qrcode'],
            ['label' => 'Evaluation Participant', 'url' => url($this->instructor === 'external' ? 'evaluation-bulk-public' : 'evaluation-bulk') . "?token=" . $token, 'icon' => 'ti ti-pencil'],
            
            // ['label' => 'Training Report', 'url' => url('training-report') . "?event_id=" . $this->id, 'icon' => 'ti ti-clipboard'],
            // ['label' => 'Documentation', 'url' => url('documentation') . "?event_id=" . $this->id, 'icon' => 'ti ti-photo'],
            // ['label' => 'Bulk Signature', 'url' => url('signature-bulk-update') . "?event_id=" . $this->id, 'icon' => 'ti ti-signature'],
            // ['label' => 'Report Evaluation', 'url' => url('evaluation-pdf') . "?event_id=" . $this->id, 'icon' => 'ti ti-file-text'],
        ];
        
        
        if ($isAdmin) {
            $arrLink[] = [
                'label' => 'Training Report',
                'url' => url('report-training') . "?event_id=" . $this->id,
                'icon' => 'ti ti-clipboard'
            ];
        
            $arrLink[] = [
                'label' => 'Documentation',
                'url' => url('documentation') . "?event_id=" . $this->id,
                'icon' => 'ti ti-photo'
            ];
        
            $arrLink[] = [
                'label' => 'Bulk Signature',
                'url' => url('signature-bulk-update') . "?event_id=" . $this->id,
                'icon' => 'ti ti-signature'
            ];
        
            $arrLink[] = [
                'label' => 'Report Evaluation',
                'url' => url('evaluation-pdf') . "?event_id=" . $this->id,
                'icon' => 'ti ti-file-text'
            ];

            if($this->instructor === 'external'){
                $arrLink[] = [
                    'label' => 'Resume Materi',
                    'url' => url('resume-materi') . "?event_id=" . $this->id,
                    'icon' => 'ti ti-file-text'
                ];
            }

            if($this->instructor === 'external'){
                $arrLink[] = [
                    'label' => 'Certification External',
                    'url' => url('certification-external') . "?event_id=" . $this->id,
                    'icon' => 'ti ti-file-certificate'
                ];
            }
        }

        
        if (
                ($trainers && !$showEvaluationMonth) || $isAdmin
        ) {
            $arrLink[] = [
                'label' => 'Evaluation 3 Month',
                'url' => url('evaluation-month') . "?token=" . $token,
                'icon' => 'ti ti-pencil'
            ];
        }

        $html = "<button type='button' data-links='" . json_encode($arrLink) . "' 
                    onclick='setMM(this)' 
                    title='Navigation' 
                    class='btn btn-outline-warning btn-sm radius-6' 
                    style='margin:1px;' 
                    data-bs-toggle='modal' 
                    data-bs-target='#modalMultiLink'>
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
