<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Dashboard;
use App\Models\Event;
use App\Models\Participant;
use App\Models\TnaAdmin;
use App\Models\Training;
use App\Models\TrainingAnalyst;
use App\Models\TrainingNeed;
use App\Models\TrainingUnplan;
use App\Models\TrainingWorkshop;
use Carbon\Carbon;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\UserRoleService;

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


    public function dashboard_old()
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
            $overdueDays = $today->greaterThan($dueDate) ? (int) $dueDate->diffInDays($today) : 0;
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
        $allowedPosition = ['Manager', 'Asman'];
        $user = Auth::user();
        if (in_array($user->role->name, $allowedRoles) || in_array($user->position, $allowedPosition) && strtoupper($user->divisi) === 'UMUM & SDM') {
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


    public function index()
    {
        $user = Auth::user();

        $currentYear = Carbon::now()->year;
        $selectedYear = (int) request('year', $currentYear);

        $role = $user->role->name ?? null;
        $divisi = $user->divisi;

        // ======================================================
        // BASE QUERY EVENT
        // ======================================================

        $eventQuery = Event::query()
            ->where('year', $selectedYear);

        // Manager & Officer → berdasarkan divisi
        if (
            $divisi &&
            strtoupper(trim($divisi)) !== 'UMUM & SDM'
        ) {
            $eventQuery->where('divisi', $divisi);
        }

        // Officer → hanya data miliknya
        if ($role !== 'developer' && $role !== 'supervisi') {
            $eventQuery->where('user_id', $user->id);
        }


        // ======================================================
        // STATISTIC EVENT
        // ======================================================

        $totalEvents = (clone $eventQuery)->count();

        $completedEvents = (clone $eventQuery)
            ->where('status', 'close')
            ->count();


        // ======================================================
        // BASE QUERY CERTIFICATE
        // ======================================================

        $certificateQuery = Certification::whereHas('event', function ($query) use (
            $selectedYear,
            $user,
            $role,
            $divisi
        ) {

            $query->where('year', $selectedYear);

            // Manager & Officer → berdasarkan divisi
            if (
                $divisi &&
                strtoupper(trim($divisi)) !== 'UMUM & SDM'
            ) {
                $query->where('divisi', $divisi);
            }

            // Officer → hanya data miliknya
            if ($role !== 'developer' && $role !== 'supervisi') {
                $query->where('user_id', $user->id);
            }
        });

        $countCertificates = $certificateQuery->count();


        // ======================================================
        // BASE QUERY TRAINING NEED / TNA
        // ======================================================

        $tnaQuery = TrainingWorkshop::whereHas('trainingNeed', function ($query) use (
            $selectedYear,
            $user,
            $role
        ) {

            $query->whereHas('training', function ($query) use (
                $selectedYear,
                $user,
                $role
            ) {

                // Filter tahun
                $query->where('year', $selectedYear);

                // Officer → hanya data miliknya
                if ($role !== 'developer' && $role !== 'supervisi') {
                    $query->where('user_id', $user->id);
                }
            });
        });

        // Manager & Officer → berdasarkan divisi
        if (
            $divisi &&
            strtoupper(trim($divisi)) !== 'UMUM & SDM'
        ) {
            $tnaQuery->where('divisi', $divisi);
        }

        $totalTNA = $tnaQuery->count();
        $tnaTerlaksana = (clone $tnaQuery)
            ->where('implementation_status', 'completed')
            ->count();

        $tnaBelumTerlaksana = (clone $tnaQuery)
            ->where('implementation_status', 'open')
            ->count();

        $trainingUnplannedQuery = TrainingUnplan::query()
            ->join('trainings', 'training_unplanes.training_id', '=', 'trainings.id')
            ->where('trainings.year', $selectedYear);
        $totalTrainingUnplanned = $trainingUnplannedQuery->count();

        $statistics = [
            [
                'title' => 'Total TNA',
                'value' => $totalTNA,
                'description' => 'Training Need Analyst',
                'icon' => 'ti-clock',
                'color' => 'warning',
            ],
            [
                'title' => 'TNA Terlaksana',
                'value' => $tnaTerlaksana,
                'description' => 'Jumlah Pelatihan tersedia',
                'icon' => 'ti-circle-check',
                'color' => 'primary',
            ],
            [
                'title' => 'TNA Belum Terlaksana',
                'value' => $tnaBelumTerlaksana,
                'description' => 'Jumlah Pelatihan tersedia',
                'icon' => 'ti-circle-x',
                'color' => 'danger',
            ],
            [
                'title' => 'Training Unplanned',
                'value' => $totalTrainingUnplanned,
                'description' => 'Jumlah Pelatihan tersedia',
                'icon' => 'ti-calendar-plus',
                'color' => 'dark',
            ],
            [
                'title' => 'Total Training',
                'value' => $totalEvents,
                'description' => 'Jumlah Pelatihan tersedia',
                'icon' => 'ti-school',
                'color' => 'info',
            ],
            [
                'title' => 'Complete Training',
                'value' => $completedEvents,
                'description' => 'Pelatihan yang terlaksana',
                'icon' => 'ti-checks',
                'color' => 'success',
            ],
            [
                'title' => 'Total Certificates',
                'value' => $countCertificates,
                'description' => 'Sertifikat yang dimiliki',
                'icon' => 'ti-certificate',
                'color' => 'secondary',
            ],
            // [
            //     'title' => 'Total TNA',
            //     'value' => $totalTNA,
            //     'description' => 'Training Need Analyst',
            //     'icon' => 'ti-clock',
            //     'color' => 'info',
            // ],
        ];

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

        $isAdmin = in_array(optional($user->role)->name, ['developer']);

        $evaluationTriMonths = Event::query()
        ->with(['trainers.user'])
        ->where('status', 'close')
        ->where('start_date', '<=', Carbon::now())
        ->where('year', $selectedYear)
        ->where(function ($query) use ($user, $isAdmin) {

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

        $evaluationIncomplete = $evaluationTriMonths->filter(function ($event) {
            $statuses = collect($event->evaluation_trainer_statuses ?? []);

            return $statuses->isNotEmpty() && $statuses->contains(fn ($status) => !$status['is_filled']);
        });

        $now = Carbon::now();
        $evaluationUpcoming = $evaluationIncomplete->filter(function ($event) use ($now) {
            return $event->evaluation_due_date->greaterThanOrEqualTo($now)
                && $event->evaluation_due_date->lessThanOrEqualTo($now->copy()->addMonthsNoOverflow(3));
        })->values();

        $evaluationNotCompleted = $evaluationTriMonths->filter(function ($event) use ($now) {
            $statuses = collect($event->evaluation_trainer_statuses ?? []);

            return $event->evaluation_due_date->lessThan($now)
                && $statuses->isNotEmpty()
                && $statuses->contains(fn ($status) => !$status['is_filled']);
        })->values();

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
        $training = Training::query()
            ->where('status', 'open')
            ->where('year', Carbon::now()->year + 1)
            ->whereNotNull('end_date')
            ->orderBy('end_date')
            ->first();
        $trainingneed = Training::query()
            ->with([
                'trainingNeeds' => fn ($query) =>
                    $query->where('divisi', $user->divisi)
            ])
            ->where('status', 'open')
            ->where('year', Carbon::now()->year + 1)
            ->whereNotNull('end_date')
            ->orderBy('end_date')
            ->first();
        $tnaAdmins = TnaAdmin::query()
            ->where('training_id', $trainingneed?->id)
            ->get();
        $data['trainingEndDate'] = $training?->end_date;
        $data['trainingId'] = $training?->id;
        $data['hasOpenNextYearTraining'] = $training !== null;
        $data['hasTrainingNeed'] = $trainingneed;
        $data['tnaAdmins'] = $tnaAdmins->pluck('user_id')->toArray();

        $allowedRoles = ['developer'];
        $allowedPosition = ['Manager', 'Asman'];
        $user = Auth::user();
        if (in_array($user->role->name, $allowedRoles) || in_array($user->position, $allowedPosition) && strtoupper($user->divisi) === 'UMUM & SDM') {
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
        $data['evaluationTriMonths'] = $evaluationUpcoming;
        $data['evaluationNotCompleted'] = $evaluationNotCompleted;

        $trainingNeedAnalyst = $this->trainingNeedAnalyst($selectedYear);
        $data['trainingNeedAnalystUpcoming'] = $trainingNeedAnalyst['upcoming'];
        $data['trainingNeedAnalystUsed'] = $trainingNeedAnalyst['used'];
        $data['trainingNeedAnalystExpired'] = $trainingNeedAnalyst['expired'];
        $data['trainingNeedAnalystCompleted'] = $trainingNeedAnalyst['completed'];
        $data['trainingNeedAnalystAll'] = $trainingNeedAnalyst['upcoming']
            ->concat($trainingNeedAnalyst['expired'])
            ->concat($trainingNeedAnalyst['completed'])
            ->sortBy('start_date')
            ->values();



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

        $data['statistics'] = $statistics;
        $data['approvalAnalisa'] = TrainingAnalyst::with('training')
            ->where('divisi', Auth::user()->divisi)
            ->where('status', 'submit')
            ->get();
        $data['approvalRencanaUsulan'] = TrainingNeed::with('training')
            ->where('divisi', Auth::user()->divisi)
            ->where('status', 'submit')
            ->get();
         $userRole = app(UserRoleService::class);
        $data['userRole'] = $userRole;
        $layout = 'backend.dashboard.dashboard';

        return view($layout, $data);
    }

    public function trainingNeedAnalyst()
    {
        $currentYear = Carbon::now()->year;
        $selectedYear = (int) request('year', $currentYear);

        $baseQuery = TrainingWorkshop::with(['trainingNeed.training', 'workshop', 'user'])
            ->whereHas('trainingNeed.training', function ($query) use ($selectedYear) {
                $query->where('year', $selectedYear);
            });

        $now = Carbon::now();

        return [
            'used' => (clone $baseQuery)
                ->where('implementation_status', 'used')
                ->orderBy('start_date')
                ->get(),
            'upcoming' => (clone $baseQuery)
                ->where('implementation_status', 'open')
                ->whereBetween('start_date', [$now, $now->copy()->endOfMonth()])
                ->orderBy('start_date')
                ->get(),
            'expired' => (clone $baseQuery)
                ->where('implementation_status', 'open')
                ->where('start_date', '<', $now)
                ->orderByDesc('start_date')
                ->get(),
            'completed' => (clone $baseQuery)
                ->where('implementation_status', 'completed')
                ->orderByDesc('start_date')
                ->get(),
        ];
    }

    public function completeTrainingWorkshop($id)
    {
        $trainingWorkshop = TrainingWorkshop::findOrFail($id);
        $trainingWorkshop->update(['implementation_status' => 'completed']);

        return response()->json([
            'status' => true,
            'message' => 'Training berhasil dipindahkan ke completed.',
        ]);
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
