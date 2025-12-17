<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Training;
use App\Models\TrainingNeed;
use App\Models\TrainingSchedule;
use App\Models\TrainingWorkshop;
use App\Models\Workshop;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TrainingScheduleController extends DefaultController
{
    protected $modelClass = TrainingSchedule::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Training Schedule';
        $this->generalUri = 'training-schedule';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_access', 'btn_edit', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Year', 'column' => 'year', 'order' => true],
            ['name' => 'Training', 'column' => 'workshop', 'order' => true],
            ['name' => 'Start date', 'column' => 'start_date', 'order' => true],
            ['name' => 'End date', 'column' => 'end_date', 'order' => true],
            ['name' => 'Instructor', 'column' => 'instructor', 'order' => true],
            ['name' => 'Position', 'column' => 'position', 'order' => true],
            ['name' => 'User', 'column' => 'user', 'order' => true],
            ['name' => 'Divisi', 'column' => 'divisi', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];

        $this->importExcelConfig = [
            'primaryKeys' => ['training_need_id'],
            'headers' => [
                ['name' => 'Training need id', 'column' => 'training_need_id'],
                ['name' => 'Workshop id', 'column' => 'workshop_id'],
                ['name' => 'Start date', 'column' => 'start_date'],
                ['name' => 'End date', 'column' => 'end_date'],
                ['name' => 'Instructor', 'column' => 'instructor'],
                ['name' => 'Position', 'column' => 'position'],
                ['name' => 'User id', 'column' => 'user_id'],
                ['name' => 'Divisi', 'column' => 'divisi'],
            ]
        ];

        $this->importScripts = [
            ['source' => asset('vendor/select2/select2.min.js')],
            ['source' => asset('vendor/select2/select2-initialize.js')]
        ];
        $this->importStyles = [
            ['source' => asset('vendor/select2/select2.min.css')],
            ['source' => asset('vendor/select2/select2-style.css')]
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $trainingId = request('training_need');
        $workshop = Workshop::select(['id as value', 'name as text'])->get();
        if ($trainingId) {
            $fieldTraining =
                [
                    'type' => 'hidden',
                    'label' => 'Training',
                    'name' =>  'training_need_id',
                    'class' => 'col-md-12 my-2',
                    'required' => $this->flagRules('training_need_id', $id),
                    'value' => (isset($edit)) ? $edit->training_need_id : request('training_need')
                ];
        } else {
            $trainingNeeds = TrainingNeed::with(['training', 'user'])->get();
            $training = $trainingNeeds->map(function ($item) {
                return [
                    'value' => $item->id,
                    'text' => ($item->training->year ?? '-') . ' - ' .
                        ($item->user->name ?? '-') . ' - ' .
                        ($item->divisi ?? '-')
                ];
            })->toArray();

            $fieldTraining =
                [
                    'type' => 'select',
                    'label' => 'Training',
                    'name' =>  'training_need_id',
                    'class' => 'col-md-12 my-2',
                    'required' => 'required',
                    'value' => (isset($edit)) ? $edit->training_need_id : '',
                    'options' => $training,
                ];
        }

        $instructor = [
            ['value' => 'internal', 'text' => 'Internal'],
            ['value' => 'external', 'text' => 'External'],
        ];

        $fields = [
            [
                'type' => 'select2',
                'label' => 'Workshop id',
                'name' =>  'workshop_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('workshop_id', $id),
                'value' => (isset($edit)) ? $edit->workshop_id : '',
                'options' => $workshop
            ],
            [
                'type' => 'datetime',
                'label' => 'Start date',
                'name' =>  'start_date',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('start_date', $id),
                'value' => (isset($edit)) ? $edit->start_date : ''
            ],
            [
                'type' => 'datetime',
                'label' => 'End date',
                'name' =>  'end_date',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('end_date', $id),
                'value' => (isset($edit)) ? $edit->end_date : ''
            ],
            [
                'type' => 'select',
                'label' => 'Instructor',
                'name' =>  'instructor',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('instructor', $id),
                'value' => (isset($edit)) ? $edit->instructor : '',
                'options' => $instructor
            ],
            [
                'type' => 'text',
                'label' => 'Position',
                'name' =>  'position',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('position', $id),
                'value' => (isset($edit)) ? $edit->position : Auth::user()->divisi
            ],
            [
                'type' => 'hidden',
                'label' => 'User id',
                'name' =>  'user_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('user_id', $id),
                'value' => (isset($edit)) ? $edit->user_id : Auth::user()->id
            ],
            [
                'type' => 'hidden',
                'label' => 'Divisi',
                'name' =>  'divisi',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('divisi', $id),
                'value' => (isset($edit)) ? $edit->divisi : Auth::user()->divisi
            ],
        ];

        $fields = array_merge([$fieldTraining], $fields);

        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
            'training_need_id' => 'required|string',
            'workshop_id' => 'required|string',
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'instructor' => 'required|string',
            'position' => 'required|string',
            'user_id' => 'required|string',
            'divisi' => 'required|string',
        ];

        return $rules;
    }


    protected function defaultDataQuery()
    {
        $filters = [];
        $orThose = null;
        $orderBy = 'id';
        $orderState = 'DESC';
        if (request('search')) {
            $orThose = request('search');
        }
        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }
        if (request('training_id')) {
            $filters[] = ['training_id', '=', request('training_id')];
        }

        $dataQueries = TrainingWorkshop::join('users', 'users.id', '=', 'training_need_workshops.user_id')
            ->join('training_needs', 'training_needs.id', '=', 'training_need_workshops.training_need_id')
            ->join('workshops', 'workshops.id', '=', 'training_need_workshops.workshop_id')
            ->join('trainings', 'trainings.id', '=', 'training_needs.training_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('training_need_workshops.start_date', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_need_workshops.end_date', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_need_workshops.instructor', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_need_workshops.position', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('trainings.year', 'LIKE', '%' . $orThose . '%');
            });

        // Cek role user
        if (Auth::user()->role->name !== ('admin')) {
            $dataQueries = $dataQueries->where('training_need_workshops.user_id', Auth::user()->id);
        }

        $dataQueries = $dataQueries
            ->select('training_need_workshops.*', 'users.name as user', 'workshops.name as workshop', 'trainings.year as year')
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }


    protected function indexApi()
    {
        $permission = (new Constant)->permissionByMenu($this->generalUri);
        $permission[] = 'access';

        $eb = [];
        $data_columns = [];
        foreach ($this->tableHeaders as $key => $col) {
            if ($key > 0) {
                $data_columns[] = $col['column'];
            }
        }

        foreach ($this->actionButtons as $key => $ab) {
            if (in_array(str_replace("btn_", "", $ab), $permission)) {
                $eb[] = $ab;
            }
        }

        $dataQueries = $this->defaultDataQuery()->paginate(10);

        $datas['extra_buttons'] = $eb;
        $datas['data_columns'] = $data_columns;
        $datas['data_queries'] = $dataQueries;
        $datas['data_permissions'] = $permission;
        $datas['uri_key'] = $this->generalUri;

        return $datas;
    }


    public function generatePDF(Request $request)
    {

        // Get all training need workshops with relationships
        $workshopsData = TrainingWorkshop::with([
            'workshop',
            'trainingNeed.training',
            'trainingNeed.user',
            'participants'
        ])
            ->when($request->training_id, function ($query) use ($request) {
                $query->whereHas('trainingNeed', function ($subQuery) use ($request) {
                    $subQuery->where('training_id', $request->training_id);
                });
            })
            ->when($request->year, function ($query) use ($request) {
                $query->whereHas('trainingNeed.training', function ($subQuery) use ($request) {
                    $subQuery->where('year', $request->year);
                });
            })
            ->get();

        if ($workshopsData->isEmpty()) {
            $trainings = [];
        } else {
            // Group workshops by division
            $trainingsByDivision = $workshopsData->groupBy('divisi');

            // Transform data according to jadwal_training.blade.php structure
            $trainings = [];
            foreach ($trainingsByDivision as $divisi => $workshops) {
                $workshopData = [];

                foreach ($workshops as $workshop) {
                    // Count participants
                    $participantCount = $workshop->participants->count();
                    if ($participantCount > 1) {
                        $participantText = $participantCount . ' Personil';
                    } else if ($participantCount === 1) {
                        $participantText = ucwords(strtolower($workshop->participants[0]->name));
                    } else {
                        $participantText = ucwords(strtolower($divisi)) . ' (TBC)';
                    }

                    // Determine which weeks/months to highlight based on start_date
                    $schedule = $this->generateScheduleArray($workshop->start_date, $workshop->end_date);

                    $workshopData[$workshop->workshop->name] = [
                        'personil' => $participantText,
                        'schedule' => $schedule
                    ];
                }

                $trainings[] = [
                    'divisi' => $divisi ?: 'Divisi Tidak Ditentukan',
                    'training' => [
                        'workshop' => $workshopData
                    ]
                ];
            }
        }

        $data = [
            'trainings' => $trainings,
            'year' => $request->year ?? date('Y'),
            'created' => Training::with(['user', 'approver'])->find($request->training_id),

        ];

        $pdf = PDF::loadView('pdf.training_schedule', $data)
            ->setPaper('A4', 'landscape');

        return $pdf->stream("Jadwal_Training_" . ($request->year ?? date('Y')) . ".pdf");
    }

    /**
     * Generate schedule array for highlighting weeks based on start and end dates
     */
    private function generateScheduleArray($startDate, $endDate)
    {
        $schedule = [
            'jan' => [],
            'feb' => [],
            'mar' => [],
            'apr' => [],
            'may' => [],
            'jun' => [],
            'jul' => [],
            'aug' => [],
            'sep' => [],
            'oct' => [],
            'nov' => [],
            'dec' => []
        ];

        if (!$startDate || !$endDate) {
            return $schedule;
        }

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Get all weeks between start and end dates
        $current = $start->copy();
        while ($current->lte($end)) {
            $monthKey = strtolower($current->format('M'));
            $weekOfMonth = ceil($current->day / 7);

            // Ensure week is between 1-4
            $weekOfMonth = min(4, max(1, $weekOfMonth));

            // Map month abbreviations
            $monthMap = [
                'jan' => 'jan',
                'feb' => 'feb',
                'mar' => 'mar',
                'apr' => 'apr',
                'may' => 'may',
                'jun' => 'jun',
                'jul' => 'jul',
                'aug' => 'aug',
                'sep' => 'sep',
                'oct' => 'oct',
                'nov' => 'nov',
                'dec' => 'dec'
            ];

            if (isset($monthMap[$monthKey]) && !in_array($weekOfMonth, $schedule[$monthMap[$monthKey]])) {
                $schedule[$monthMap[$monthKey]][] = $weekOfMonth;
            }

            $current->addWeek();
        }

        return $schedule;
    }
}
