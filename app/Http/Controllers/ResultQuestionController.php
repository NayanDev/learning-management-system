<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ResultQuestion;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Support\Facades\Auth;

class ResultQuestionController extends DefaultController
{
    protected $modelClass = ResultQuestion::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Result Question';
        $this->generalUri = 'result-question';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_multilink', 'btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Training', 'column' => 'workshop', 'order' => true],
            ['name' => 'Name', 'column' => 'name', 'order' => true],
            ['name' => 'Type', 'column' => 'type', 'order' => true],
            ['name' => 'Pretest (score)', 'column' => 'pretest', 'order' => true],
            ['name' => 'Posttest (1)(score)', 'column' => 'posttest', 'order' => true],
            ['name' => 'Posttest (2)(score)', 'column' => 'posttest2_score', 'order' => true],
            ['name' => 'User', 'column' => 'user', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['event_id'],
            'headers' => [
                ['name' => 'Event id', 'column' => 'event_id'],
                ['name' => 'User id', 'column' => 'user_id'],
                ['name' => 'Type', 'column' => 'type'],
                ['name' => 'Pretest score', 'column' => 'pretest_score'],
                ['name' => 'Posttest1 score', 'column' => 'posttest1_score'],
                ['name' => 'Posttest2 score', 'column' => 'posttest2_score'],
            ]
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $fields = [
            [
                'type' => 'text',
                'label' => 'Participant id',
                'name' =>  'participant_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('participant_id', $id),
                'value' => (isset($edit)) ? $edit->participant_id : ''
            ],
            [
                'type' => 'text',
                'label' => 'User id',
                'name' =>  'user_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('user_id', $id),
                'value' => (isset($edit)) ? $edit->user_id : ''
            ],
            [
                'type' => 'text',
                'label' => 'Type',
                'name' =>  'type',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('type', $id),
                'value' => (isset($edit)) ? $edit->type : ''
            ],
            [
                'type' => 'text',
                'label' => 'Pretest score',
                'name' =>  'pretest_score',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('pretest_score', $id),
                'value' => (isset($edit)) ? $edit->pretest_score : ''
            ],
            [
                'type' => 'text',
                'label' => 'Posttest1 score',
                'name' =>  'posttest1_score',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('posttest1_score', $id),
                'value' => (isset($edit)) ? $edit->posttest1_score : ''
            ],
            [
                'type' => 'text',
                'label' => 'Posttest2 score',
                'name' =>  'posttest2_score',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('posttest2_score', $id),
                'value' => (isset($edit)) ? $edit->posttest2_score : ''
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
        if (request('event_id')) {
            $filters[] = ['participants.event_id', '=', request('event_id')];
        }

        $dataQueries = ResultQuestion::rightJoin('participants', 'participants.id', '=', 'result_questions.participant_id')
            ->join('events', 'events.id', '=', 'participants.event_id')
            ->join('workshops', 'workshops.id', '=', 'events.workshop_id')
            ->join('users', 'users.id', '=', 'participants.user_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('result_questions.type', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('result_questions.type', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('result_questions.pretest_score', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('result_questions.posttest1_score', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('result_questions.posttest2_score', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('participants.name', 'LIKE', '%' . $orThose . '%');
            });

        // Cek role user
        if (Auth::user()->role->name === 'participant') {
            $dataQueries = $dataQueries->where('participants.nik', Auth::user()->nik);
        }

        if (Auth::user()->role->name !== 'admin') {
            $dataQueries = $dataQueries->where('participants.user_id', Auth::user()->id);
        }

        $dataQueries = $dataQueries
            ->select('result_questions.*', 'users.name as user', 'participants.name as name', 'participants.divisi as divisi', 'workshops.name as workshop')
            ->orderBy($orderBy, $orderState);

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
        $data['actionButtonViews'] = [
            'easyadmin::backend.idev.buttons.delete',
            'easyadmin::backend.idev.buttons.edit',
            'easyadmin::backend.idev.buttons.show',
            'easyadmin::backend.idev.buttons.import_default',
            'backend.idev.buttons.multilink',
        ];
        $data['templateImportExcel'] = "#";
        $data['filters'] = $this->filters();

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'easyadmin::backend.idev.list_drawer';

        return view($layout, $data);
    }


    protected function filters()
    {
        if (Auth::user()->role->name === 'admin') {
            $isEvent = Event::get();
        } else {
            $isEvent = Event::where('user_id', Auth::user()->id)->get();
        }
        $arrEvent = [];
        $arrEvent[] = ['value' => "", 'text' => "All Event"];
        foreach ($isEvent as $key => $event) {
            $arrEvent[] = ['value' => $event->id, 'text' => $event->workshop->name];
        }

        $eventId = request('event_id');
        if (!$eventId) {
            $fields = [
                [
                    'type' => 'select2',
                    'label' => 'Event',
                    'name' => 'event_id',
                    'class' => 'col-md-2',
                    'options' => $arrEvent,
                ],
            ];
        } else {
            $fields = [];
        }

        return $fields;
    }
}
