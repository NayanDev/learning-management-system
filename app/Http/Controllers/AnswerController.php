<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Event;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Support\Facades\Auth;

class AnswerController extends DefaultController
{
    protected $modelClass = Answer::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Answer';
        $this->generalUri = 'answer';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Name', 'column' => 'name', 'order' => true],
            ['name' => 'Question', 'column' => 'question', 'order' => true],
            ['name' => 'Point', 'column' => 'point', 'order' => true],
            ['name' => 'User', 'column' => 'user', 'order' => true],
            ['name' => 'Divisi', 'column' => 'divisi', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['name'],
            'headers' => [
                ['name' => 'Name', 'column' => 'name'],
                ['name' => 'Question id', 'column' => 'question_id'],
                ['name' => 'Point', 'column' => 'point'],
                ['name' => 'User id', 'column' => 'user_id'],
                ['name' => 'Divisi', 'column' => 'divisi'],
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
                'label' => 'Name',
                'name' =>  'name',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('name', $id),
                'value' => (isset($edit)) ? $edit->name : ''
            ],
            [
                'type' => 'text',
                'label' => 'Question id',
                'name' =>  'question_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('question_id', $id),
                'value' => (isset($edit)) ? $edit->question_id : ''
            ],
            [
                'type' => 'text',
                'label' => 'Point',
                'name' =>  'point',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('point', $id),
                'value' => (isset($edit)) ? $edit->point : ''
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
                'label' => 'Divisi',
                'name' =>  'divisi',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('divisi', $id),
                'value' => (isset($edit)) ? $edit->divisi : ''
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
            $filters[] = ['questions.event_id', '=', request('event_id')];
        }
        if (request('question_id')) {
            $filters[] = ['answers.question_id', '=', request('question_id')];
        }

        $dataQueries = Answer::join('questions', 'questions.id', '=', 'answers.question_id')
            ->join('users', 'users.id', '=', 'answers.user_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('answers.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('questions.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('answers.point', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
            });

        // Cek role user
        if (Auth::user()->role->name !== 'admin') {
            $dataQueries = $dataQueries->where('answers.user_id', Auth::user()->id);
        }

        $dataQueries = $dataQueries
            ->select('answers.*', 'users.name as user', 'questions.name as question')
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
        if (request('question_id')) {
            $params = "?question_id=" . request('question_id');
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
