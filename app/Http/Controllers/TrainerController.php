<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Trainer;
use App\Models\User;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Support\Facades\Auth;

class TrainerController extends DefaultController
{
    protected $modelClass = Trainer::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Trainer';
        $this->generalUri = 'trainer';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Training', 'column' => 'workshop', 'order' => true],
            ['name' => 'User', 'column' => 'user', 'order' => true],
            ['name' => 'External', 'column' => 'external', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['event_id'],
            'headers' => [
                ['name' => 'Event id', 'column' => 'event_id'],
                ['name' => 'User id', 'column' => 'user_id'],
                ['name' => 'External', 'column' => 'external'],
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

        $user = User::select(['id as value', 'name as text'])
            ->get()
            ->prepend([
                'value' => '',
                'text'  => 'Pilih peserta',
            ])
            ->toArray();
        $events = Event::with(['workshop'])->get();
        $training = $events->map(function ($item) {
            return [
                'value' => $item->id,
                'text'  => $item->workshop->name ?? '-',
            ];
        })->toArray();

        $eventId = request('event_id');
        $eventData = Event::find($eventId);
        if (!$eventId) {

            $fields = [
                [
                    'type' => 'select2',
                    'label' => 'Training',
                    'name' =>  'event_id',
                    'class' => 'col-md-12 my-2',
                    'required' => 'required',
                    'value' => (isset($edit)) ? $edit->event_id : '',
                    'options' => $training,
                ],
                [
                    'type' => 'select2',
                    'label' => 'Trainer (internal)',
                    'name' =>  'user_id',
                    'class' => 'col-md-12 my-2',
                    'required' => 'required',
                    'value' => (isset($edit)) ? $edit->user_id : '',
                    'options' => $user,
                ],
                [
                    'type' => 'text',
                    'label' => 'Trainer (external)',
                    'name' =>  'external',
                    'class' => 'col-md-12 my-2',
                    'required' => $this->flagRules('external', $id),
                    'value' => (isset($edit)) ? $edit->external : ''
                ],
            ];
        } else {
            if ($eventData->instructor === 'internal') {
                $fields = [
                    [
                        'type' => 'select2',
                        'label' => 'Trainer (internal)',
                        'name' =>  'user_id',
                        'class' => 'col-md-12 my-2',
                        'required' => 'required',
                        'value' => (isset($edit)) ? $edit->user_id : '',
                        'options' => $user,
                    ],
                    [
                        'type' => 'hidden',
                        'label' => 'External',
                        'name' =>  'external',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('external', $id),
                        'value' => (isset($edit)) ? $edit->external : ''
                    ],
                    [
                        'type' => 'hidden',
                        'label' => 'Training',
                        'name' =>  'event_id',
                        'class' => 'col-md-12 my-2',
                        'required' => 'required',
                        'value' => (isset($edit)) ? $edit->event_id : $eventId,
                    ]
                ];
            } else {
                $fields = [
                    [
                        'type' => 'text',
                        'label' => 'External',
                        'name' =>  'external',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('external', $id),
                        'value' => (isset($edit)) ? $edit->external : ''
                    ],
                    [
                        'type' => 'hidden',
                        'label' => 'Trainer (internal)',
                        'name' =>  'user_id',
                        'class' => 'col-md-12 my-2',
                        'required' => 'required',
                        'value' => (isset($edit)) ? $edit->user_id : '',
                    ],
                    [
                        'type' => 'hidden',
                        'label' => 'Training',
                        'name' =>  'event_id',
                        'class' => 'col-md-12 my-2',
                        'required' => 'required',
                        'value' => (isset($edit)) ? $edit->event_id : $eventId,
                    ]
                ];
            }
        }

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
        if (request('event_id')) {
            $filters[] = ['trainers.event_id', '=', request('event_id')];
        }

        $dataQueries = Trainer::join('events', 'events.id', '=', 'trainers.event_id')
            ->leftJoin('users', 'users.id', '=', 'trainers.user_id')
            ->join('workshops', 'workshops.id', '=', 'events.workshop_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('trainers.external', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
            });

        // Cek role user
        if (Auth::user()->role->name !== 'admin') {
            $dataQueries = $dataQueries->where('trainers.user_id', Auth::user()->id);
        }

        $dataQueries = $dataQueries
            ->select('trainers.*', 'workshops.name as workshop', 'users.name as user')
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }


    public function index()
    {
        $moreActions = [
            [
                'key' => 'import-excel-default',
                'name' => 'Import Excel',
                'html_button' => "<button id='import-excel' type='button' class='btn btn-sm btn-info radius-6' href='#' data-bs-toggle='modal' data-bs-target='#modalImportDefault' title='Import Excel' ><i class='ti ti-upload'></i></button>"
            ],
            [
                'key' => 'export-excel-default',
                'name' => 'Export Excel',
                'html_button' => "<a id='export-excel' class='btn btn-sm btn-success radius-6' target='_blank' href='" . url($this->generalUri . '-export-excel-default') . "'  title='Export Excel'><i class='ti ti-cloud-download'></i></a>"
            ],
            [
                'key' => 'export-pdf-default',
                'name' => 'Export Pdf',
                'html_button' => "<a id='export-pdf' class='btn btn-sm btn-danger radius-6' target='_blank' href='" . url($this->generalUri . '-export-pdf-default') . "' title='Export PDF'><i class='ti ti-file'></i></a>"
            ],
        ];

        $params = "";
        if (request('event_id')) {
            $params = "?event_id=" . request('event_id');
        }

        $permissions = (new Constant())->permissionByMenu($this->generalUri);
        $data['permissions'] = $permissions;
        $data['more_actions'] = $moreActions;
        $data['table_headers'] = $this->tableHeaders;
        $data['title'] = $this->title;
        $data['uri_key'] = $this->generalUri;
        $data['uri_list_api'] = route($this->generalUri . '.listapi') . $params;
        $data['uri_create'] = route($this->generalUri . '.create');
        $data['url_store'] = route($this->generalUri . '.store');
        $data['fields'] = $this->fields();
        $data['edit_fields'] = $this->fields();
        $data['actionButtonViews'] = $this->actionButtonViews;
        $data['templateImportExcel'] = "#";
        $data['filters'] = $this->filters();

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'easyadmin::backend.idev.list_drawer';

        return view($layout, $data);
    }
}
