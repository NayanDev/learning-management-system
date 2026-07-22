<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Dashboard;
use App\Models\Event;
use App\Models\Participant;
use App\Models\TrainingWorkshop;
use Carbon\Carbon;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends DefaultController
{
    protected $modelClass = Dashboard::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Dashboard';
        $this->generalUri = 'dashboard';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => [''],
            'headers' => []
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $fields = [];

        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [];

        return $rules;
    }


    public function index()
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;
        $selectedYear = (int) request('year', $currentYear);

        $availableYears = Event::query()
            ->whereNotNull('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($year) => (int) $year)
            ->values()
            ->all();

        if (!in_array($currentYear, $availableYears, true)) {
            array_unshift($availableYears, $currentYear);
        }

        $availableYears = array_values(array_unique($availableYears));

        $evaluationTriMonths = Event::query()
        ->with(['trainers.user'])
        ->where('status', 'close')
        ->where('start_date', '<', Carbon::now()->subMonths(3))
        ->where('year', $selectedYear)
        ->where(function ($query) use ($user) {

            $isAdmin = in_array(optional($user->role)->name, ['admin', 'adminhr']);

            // kalau admin, langsung lolos
            if ($isAdmin) {
                $query->whereRaw('1 = 1');
                return;
            }

            // kondisi non-admin
            $query->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('trainers')
                    ->whereColumn('trainers.event_id', 'events.id')
                    ->where('trainers.user_id', Auth::id());
            })
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('evaluation_months')
                    ->whereColumn('evaluation_months.event_id', 'events.id')
                    ->where('evaluation_months.user_id', Auth::id());
            });
        })
        ->get();

        $evaluationTriMonths = $evaluationTriMonths->map(function ($event) {
            $dueDate = Carbon::parse($event->start_date)->addMonthsNoOverflow(3);
            $today = Carbon::today();
            $overdueDays = $today->greaterThan($dueDate) ? $dueDate->diffInDays($today) : 0;
            $filledTrainerIds = DB::table('evaluation_months')
                ->where('event_id', $event->id)
                ->distinct()
                ->pluck('user_id')
                ->filter()
                ->values()
                ->all();

            $event->evaluation_due_date = $dueDate;
            $event->evaluation_overdue_days = $overdueDays;
            $event->evaluation_trainer_statuses = $event->trainers
                ->map(function ($trainer) use ($filledTrainerIds, $overdueDays) {
                    $trainerName = $trainer->user->name ?? $trainer->external ?? 'trainer';
                    $isFilled = in_array($trainer->user_id, $filledTrainerIds);

                    return [
                        'name' => $trainerName,
                        'is_filled' => $isFilled,
                        'overdue_badge' => (!$isFilled && $overdueDays > 0)
                            ? '+' . (int) $overdueDays . ' hari'
                            : null,
                    ];
                })
                ->values()
                ->all();

            return $event;
        });

        $isAdmin = in_array(optional($user->role)->name, ['admin', 'adminhr']);

        $upcomingTraining = Event::query()
        ->select('events.*')
        ->selectRaw('EXISTS (
            SELECT 1 FROM trainers 
            WHERE trainers.event_id = events.id 
            AND trainers.user_id = ?
        ) as is_trainer', [Auth::id()])
        ->where('status', 'open')
        ->whereYear('start_date', $currentYear)
        ->where('start_date', '>', Carbon::now())
        ->when(!$isAdmin, function ($query) {
            $query->where(function ($q) {
                $q->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('trainers')
                        ->whereColumn('trainers.event_id', 'events.id')
                        ->where('trainers.user_id', Auth::id());
                })
                ->orWhereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('participants')
                        ->whereColumn('participants.event_id', 'events.id')
                        ->where('participants.nik', Auth::user()->nik);
                });
            });
        })
        ->get();

        $data['title'] = $this->title;
        $data['selectedYear'] = $selectedYear;
        $data['availableYears'] = $availableYears;

        $allowedRoles = ['adminhr', 'admin'];
        $user = Auth::user();
        if (in_array($user->role->name, $allowedRoles)) {
            // tampilkan semua data
            $data['totalEvents'] = Event::where('year', $selectedYear)->count();
            $data['completedEvents'] = Event::where('year', $selectedYear)->where('status', 'close')->count();
            $data['upcomingEvents'] = Event::where('year', $selectedYear)->where('status', 'open')->count();
            $data['totalTrainingWorkshops'] = TrainingWorkshop::whereHas('trainingNeed', function ($query) use ($selectedYear) {
                $query->whereHas('training', function ($trainingQuery) use ($selectedYear) {
                    $trainingQuery->where('year', $selectedYear);
                });
            })->count();
            $data['countCertificates'] = Certification::whereHas('event', function ($query) use ($selectedYear) {
                $query->where('year', $selectedYear);
            })->count();
        } else {
            // filter by user
            $data['totalEvents'] = Event::where('user_id', $user->id)->where('year', $selectedYear)->count();
            $data['completedEvents'] = Event::where('status', 'close')->where('user_id', $user->id)->where('year', $selectedYear)->count();
            $data['upcomingEvents'] = Event::where('status', 'open')->where('user_id', $user->id)->where('year', $selectedYear)->count();
            $data['totalTrainingWorkshops'] = TrainingWorkshop::where('user_id', $user->id)->whereHas('trainingNeed', function ($query) use ($selectedYear) {
                $query->whereHas('training', function ($trainingQuery) use ($selectedYear) {
                    $trainingQuery->where('year', $selectedYear);
                });
            })->count();
            $data['countCertificates'] = Certification::whereHas('participant', function ($query) use ($user) {
                $query->where('nik', $user->nik);
            })->whereHas('event', function ($query) use ($selectedYear) {
                $query->where('year', $selectedYear);
            })->count();
        }

        $data['trainingRealizationPercentage'] = $data['totalTrainingWorkshops'] > 0
            ? round(($data['totalEvents'] * 100) / $data['totalTrainingWorkshops'], 2)
            : 0;

        $data['trainingcoomingsoon'] = $upcomingTraining;
        $data['evaluationTriMonths'] = $evaluationTriMonths;





        $data['eventsAttendance'] = $this->takeTrainingAttendance($selectedYear);
        
        // Get TNA data from TrainingWorkshop
        $data['totalTNA'] = \App\Models\TrainingWorkshop::whereHas('trainingNeed', function($q) use ($selectedYear) {
            $q->whereHas('training', function($q2) use ($selectedYear) {
                $q2->where('year', $selectedYear);
            });
        })->count();
        
        // TNA yang sudah terlaksana (ada event yang sudah selesai)
        $data['completedTNA'] = \App\Models\TrainingWorkshop::whereHas('trainingNeed', function($q) use ($selectedYear) {
            $q->whereHas('training', function($q2) use ($selectedYear) {
                $q2->where('year', $selectedYear);
            });
        })->whereHas('workshop', function($q) {
            $q->whereHas('events', function($q2) {
                $q2->where('end_date', '<', Carbon::now());
            });
        })->count();
        
        // Calculate percentage
        $data['tnaPercentage'] = $data['totalTNA'] > 0 
            ? round(($data['completedTNA'] / $data['totalTNA']) * 100, 2) 
            : 0;

        $layout = 'backend.idev.participant_dashboard';

        return view($layout, $data);
    }


    public function takeTrainingAttendance()
    {
        $user = Auth::user();

        return Participant::with('event', 'attendance')
            ->where('nik', $user->nik)
            ->whereHas('event', function ($query) {
                $query->where('end_date', '>=', Carbon::today());
            })->get();
    }


    public function qrcodeScanner()
    {
        return view('backend.idev.qrcode_scanner');
    }
}
