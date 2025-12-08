<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use App\Models\Training;
use App\Models\TrainingUnplan;
use App\Models\TrainingUnplanParticipant;
use App\Models\Workshop;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Log;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrainingUnplanController extends DefaultController
{
    protected $modelClass = TrainingUnplan::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Training Unplan';
        $this->generalUri = 'training-unplan';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_approve', 'btn_access', 'btn_edit', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Year', 'column' => 'year', 'order' => true],
            ['name' => 'User', 'column' => 'user', 'order' => true],
            ['name' => 'Training', 'column' => 'workshop', 'order' => true],
            ['name' => 'Organizer', 'column' => 'organizer', 'order' => true],
            ['name' => 'Start date', 'column' => 'start_date', 'order' => true],
            ['name' => 'End date', 'column' => 'end_date', 'order' => true],
            ['name' => 'Divisi', 'column' => 'divisi', 'order' => true],
            ['name' => 'Instructor', 'column' => 'instructor', 'order' => true],
            ['name' => 'Location', 'column' => 'location', 'order' => true],
            ['name' => 'Status', 'column' => 'badge_status', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['training_id'],
            'headers' => [
                ['name' => 'Training id', 'column' => 'training_id'],
                ['name' => 'User id', 'column' => 'user_id'],
                ['name' => 'Workshop id', 'column' => 'workshop_id'],
                ['name' => 'Organizer', 'column' => 'organizer'],
                ['name' => 'Start date', 'column' => 'start_date'],
                ['name' => 'End date', 'column' => 'end_date'],
                ['name' => 'Divisi', 'column' => 'divisi'],
                ['name' => 'Instructor', 'column' => 'instructor'],
                ['name' => 'Location', 'column' => 'location'],
                ['name' => 'Status', 'column' => 'status'],
                ['name' => 'Notes', 'column' => 'notes'],
                ['name' => 'Approve by', 'column' => 'approve_by'],
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

        $workshop = Workshop::select(['id as value', 'name as text'])->get();
        $instructor = [
            ['value' => 'internal', 'text' => 'Internal'],
            ['value' => 'external', 'text' => 'External'],
        ];

        $trainingId = request('training_id');
        if ($trainingId) {
            $fieldTraining =
                [
                    'type' => 'hidden',
                    'label' => 'Training',
                    'name' =>  'training_id',
                    'class' => 'col-md-12 my-2',
                    'required' => $this->flagRules('training_id', $id),
                    'value' => (isset($edit)) ? $edit->training_id : request('training_id')
                ];
        } else {
            $training = Training::select(['id as value', 'year as text'])->get();
            $fieldTraining =
                [
                    'type' => 'select',
                    'label' => 'Training',
                    'name' =>  'training_id',
                    'class' => 'col-md-12 my-2',
                    'required' => 'required',
                    'value' => (isset($edit)) ? $edit->training_id : '',
                    'options' => $training,
                ];
        }

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
                'type' => 'text',
                'label' => 'Organizer',
                'name' =>  'organizer',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('organizer', $id),
                'value' => (isset($edit)) ? $edit->organizer : ''
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
                'type' => 'text',
                'label' => 'Divisi',
                'name' =>  'divisi',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('divisi', $id),
                'value' => (isset($edit)) ? $edit->divisi : Auth::user()->divisi
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
                'label' => 'Location',
                'name' =>  'location',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('location', $id),
                'value' => (isset($edit)) ? $edit->location : ''
            ],
            [
                'type' => 'hidden',
                'label' => 'User id',
                'name' =>  'user_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('user_id', $id),
                'value' => (isset($edit)) ? $edit->user_id : Auth::user()->id
            ],
        ];

        $fields = array_merge([$fieldTraining], $fields);

        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [];

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

        $dataQueries = TrainingUnplan::join('users', 'users.id', '=', 'training_unplanes.user_id')
            ->join('workshops', 'workshops.id', '=', 'training_unplanes.workshop_id')
            ->join('trainings', 'trainings.id', '=', 'training_unplanes.training_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('training_unplanes.organizer', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_unplanes.start_date', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_unplanes.end_date', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_unplanes.divisi', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_unplanes.instructor', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_unplanes.location', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_unplanes.status', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('trainings.year', 'LIKE', '%' . $orThose . '%');
            });

        // Cek role user
        if (Auth::user()->role->name !== ('admin')) {
            $dataQueries = $dataQueries->where('training_unplanes.user_id', Auth::user()->id);
        }

        $dataQueries = $dataQueries
            ->select('training_unplanes.*', 'users.name as user', 'workshops.name as workshop', 'trainings.year as year')
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }


    public function approve(Request $request, $id)
    {
        $training = TrainingUnplan::findOrFail($id);

        if ($request->status === 'approve') {
            $this->copyTrainingDataToEvents($id);
        }

        $training->status = $request->status;
        $training->approve_by = $request->approve_by;
        $training->notes = $request->notes ?: '-';
        $training->updated_at = now();
        $training->save();

        return response()->json([
            'status' => true,
            'message' => 'Status updated successfully'
        ]);
    }


    protected function indexApi()
    {
        $permission = (new Constant)->permissionByMenu($this->generalUri);
        $permission[] = 'approve';
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


    public function index()
    {
        $baseUrlExcel = route($this->generalUri . '.export-excel-default');
        $baseUrlPdf = route($this->generalUri . '.export-pdf-default');

        $moreActions = [
            [
                'key' => 'import-excel-default',
                'name' => 'Import Excel',
                'html_button' => "<button id='import-excel' type='button' class='btn btn-sm btn-info radius-6' href='#' data-bs-toggle='modal' data-bs-target='#modalImportDefault' title='Import Excel' ><i class='ti ti-upload'></i></button>"
            ],
            [
                'key' => 'export-excel-default',
                'name' => 'Export Excel',
                'html_button' => "<a id='export-excel' data-base-url='" . $baseUrlExcel . "' class='btn btn-sm btn-success radius-6' target='_blank' href='" . url($this->generalUri . '-export-excel-default') . "'  title='Export Excel'><i class='ti ti-cloud-download'></i></a>"
            ],
            [
                'key' => 'export-pdf-default',
                'name' => 'Export Pdf',
                'html_button' => "<a id='export-pdf' data-base-url='" . $baseUrlPdf . "' class='btn btn-sm btn-danger radius-6' target='_blank' href='" . url($this->generalUri . '-export-pdf-default') . "' title='Export PDF'><i class='ti ti-file'></i></a>"
            ],
        ];

        $permissions =  $this->arrPermissions;
        if ($this->dynamicPermission) {
            $permissions = (new Constant())->permissionByMenu($this->generalUri);
        }
        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'easyadmin::backend.idev.list_drawer';
        if (isset($this->drawerLayout)) {
            $layout = $this->drawerLayout;
        }
        $data['permissions'] = $permissions;
        $data['more_actions'] = $moreActions;
        $data['headerLayout'] = $this->pageHeaderLayout;
        $data['table_headers'] = $this->tableHeaders;
        $data['title'] = $this->title;
        $data['uri_key'] = $this->generalUri;
        $data['uri_list_api'] = route($this->generalUri . '.listapi');
        $data['uri_create'] = route($this->generalUri . '.create');
        $data['url_store'] = route($this->generalUri . '.store');
        $data['fields'] = $this->fields();
        $data['edit_fields'] = $this->fields('edit');
        $data['actionButtonViews'] = [
            'easyadmin::backend.idev.buttons.delete',
            'easyadmin::backend.idev.buttons.edit',
            'easyadmin::backend.idev.buttons.show',
            'easyadmin::backend.idev.buttons.import_default',
            'backend.idev.buttons.approve',
        ];
        $data['templateImportExcel'] = "#";
        $data['import_scripts'] = $this->importScripts;
        $data['import_styles'] = $this->importStyles;
        $data['filters'] = $this->filters();

        return view($layout, $data);
    }


    protected function copyTrainingDataToEvents($trainingId)
    {
        try {
            // Ambil data training unplan
            $trainingUnplane = TrainingUnplan::where('id', $trainingId)->first();
            if (!$trainingUnplane) {
                throw new Exception('Training Unplan not found');
            }

            // Ambil data training
            $training = Training::findOrFail($trainingUnplane->training_id);

            // Ambil data peserta
            $trainingUnplaneParticipant = TrainingUnplanParticipant::where('training_unplane_id', $trainingUnplane->id)->get();

            // Buat event baru
            $event = new Event();
            $event->workshop_id = $trainingUnplane->workshop_id;
            $event->user_id = $trainingUnplane->user_id;
            $event->year = $training->year;
            $event->organizer = $trainingUnplane->organizer;
            $event->start_date = $trainingUnplane->start_date;
            $event->end_date = $trainingUnplane->end_date;
            $event->divisi = $trainingUnplane->divisi;
            $event->instructor = $trainingUnplane->instructor;
            $event->location = $trainingUnplane->location;
            $event->approve_by = null;
            $event->created_date = null;
            $event->notes = null;
            $event->status = 'open';
            $event->token = Str::random(32);
            $event->token_expired = Carbon::parse($trainingUnplane->start_date)->addHour(12);
            $event->created_at = now();
            $event->updated_at = now();
            $event->save();

            // Pengecekan apakah ada data peserta
            if (!$trainingUnplaneParticipant->isEmpty()) {
                $participantsData = [];
                foreach ($trainingUnplaneParticipant as $participant) {
                    $participantsData[] = [
                        'name' => $participant->name,
                        'divisi' => $participant->divisi,
                        'company' => $participant->company,
                        'nik' => $participant->nik,
                        'unit_kerja' => $participant->unit_kerja,
                        'status' => $participant->status,
                        'jk' => $participant->jk,
                        'email' => $participant->email,
                        'telp' => $participant->telp,
                        'user_id' => $participant->user_id ?: null,  // Menangani user_id kosong
                        'event_id' => $event->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Insert peserta sekaligus
                if (!empty($participantsData)) {
                    Participant::insert($participantsData);
                }
            } else {
                // Jika tidak ada data peserta, lewati proses penyimpanan peserta
                Log::info("No participant found for training_unplane_id: " . $trainingUnplane->id);
            }
        } catch (Exception $e) {
            Log::error('Error copying training data: ' . $e->getMessage());
            throw new Exception('Error copying training data: ' . $e->getMessage());
        }
    }
}
