<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Workshop;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends DefaultController
{
    protected $modelClass = Event::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Event';
        $this->generalUri = 'event';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_multilink', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Training', 'column' => 'workshop', 'order' => true],
            ['name' => 'User', 'column' => 'created_by', 'order' => true],
            ['name' => 'Year', 'column' => 'year', 'order' => true],
            ['name' => 'Participant', 'column' => 'participant_count', 'order' => true],
            ['name' => 'Letter number', 'column' => 'letter_number', 'order' => true],
            ['name' => 'Organizer', 'column' => 'organizer', 'order' => true],
            ['name' => 'Trainer', 'column' => 'trainers', 'order' => true],
            ['name' => 'Location', 'column' => 'location', 'order' => true],
            ['name' => 'Start date', 'column' => 'start_date', 'order' => true],
            ['name' => 'End date', 'column' => 'end_date', 'order' => true],
            ['name' => 'Divisi', 'column' => 'divisi', 'order' => true],
            ['name' => 'Instructor', 'column' => 'instructor', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['workshop_id'],
            'headers' => [
                ['name' => 'Workshop id', 'column' => 'workshop_id'],
                ['name' => 'User id', 'column' => 'user_id'],
                ['name' => 'Divisi', 'column' => 'divisi'],
                ['name' => 'Year', 'column' => 'year'],
                ['name' => 'Letter number', 'column' => 'letter_number'],
                ['name' => 'Organizer', 'column' => 'organizer'],
                ['name' => 'Start date', 'column' => 'start_date'],
                ['name' => 'End date', 'column' => 'end_date'],
                ['name' => 'Token', 'column' => 'token'],
                ['name' => 'Token expired', 'column' => 'token_expired'],
                ['name' => 'Instructor', 'column' => 'instructor'],
                ['name' => 'Location', 'column' => 'location'],
                ['name' => 'Approve by', 'column' => 'approve_by'],
                ['name' => 'Created date', 'column' => 'created_date'],
                ['name' => 'Notes', 'column' => 'notes'],
                ['name' => 'Status', 'column' => 'status'],
            ]
        ];


        $this->importScripts = [
            ['source' => asset('vendor/select2/select2.min.js')],
            ['source' => asset('vendor/select2/select2-initialize.js')],
        ];
        $this->importStyles = [
            ['source' => asset('vendor/select2/select2.min.css')],
            ['source' => asset('vendor/select2/select2-style.css')],
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

        $fields = [
            [
                'type' => 'onlyview',
                'label' => 'Divisi',
                'name' =>  'divisi',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('name', $id),
                'value' => (isset($edit)) ? $edit->divisi : Auth::user()->divisi
            ],
            [
                'type' => 'select2',
                'label' => 'Workshop',
                'name' =>  'workshop_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('name', $id),
                'value' => (isset($edit)) ? $edit->workshop_id : '',
                'options' => $workshop
            ],
            [
                'type' => 'text',
                'label' => 'Letter number',
                'name' =>  'letter_number',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('letter_number', $id),
                'value' => (isset($edit)) ? $edit->letter_number : ''
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
                'label' => 'User',
                'name' =>  'user_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('user_id', $id),
                'value' => (isset($edit)) ? $edit->user_id : ''
            ],
        ];

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

        $dataQueries = Event::leftJoin('participants', 'participants.event_id', '=', 'events.id')
            ->leftJoin('trainers', 'trainers.event_id', '=', 'events.id')
            ->leftJoin('users', 'users.id', '=', 'trainers.user_id')
            ->join('workshops', 'workshops.id', '=', 'events.workshop_id')
            ->join('users as creator', 'creator.id', '=', 'events.user_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('events.start_date', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('events.end_date', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('events.year', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('events.letter_number', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('events.organizer', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('events.location', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('events.divisi', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('events.instructor', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
            });

        // Cek role user
        if (Auth::user()->role->name !== ('admin')) {
            $dataQueries = $dataQueries->where('events.user_id', Auth::user()->id);
        }

        $dataQueries = $dataQueries
            ->select(
                'events.id',
                'events.year',
                'events.start_date',
                'events.end_date',
                'events.letter_number',
                'events.organizer',
                'events.location',
                'events.divisi',
                'events.instructor',
                'events.created_at',
                'events.updated_at',
                'workshops.name as workshop',
                'creator.name as created_by',
                DB::raw("
                    CASE
                        WHEN COUNT(DISTINCT participants.id) = 0 THEN 'TBC'
                        ELSE CONCAT(COUNT(DISTINCT participants.id), ' personil')
                    END as participant_count
                "),
                DB::raw('GROUP_CONCAT(DISTINCT COALESCE(users.name, trainers.external) SEPARATOR ", ") as trainers')
            )
            ->groupBy(
                'events.id',
                'events.year',
                'events.start_date',
                'events.end_date',
                'events.letter_number',
                'events.organizer',
                'events.location',
                'events.divisi',
                'events.instructor',
                'events.created_at',
                'events.updated_at',
                'workshops.name',
                'creator.name'
            )->orderBy($orderBy, $orderState);

        return $dataQueries;
    }


    protected function indexApi()
    {
        $permission = (new Constant)->permissionByMenu($this->generalUri);
        $permission[] = 'multilink';

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
            'backend.idev.buttons.multilink',
        ];
        $data['templateImportExcel'] = "#";
        $data['import_scripts'] = $this->importScripts;
        $data['import_styles'] = $this->importStyles;
        $data['filters'] = $this->filters();

        return view($layout, $data);
    }
}
