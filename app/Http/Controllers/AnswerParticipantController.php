<?php

namespace App\Http\Controllers;

use App\Models\AnswerParticipant;
use App\Models\Event;
use App\Models\Participant;
use App\Models\User;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Support\Facades\Auth;

class AnswerParticipantController extends DefaultController
{
    protected $modelClass = AnswerParticipant::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Answer Participant';
        $this->generalUri = 'answer-participant';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Training', 'column' => 'workshop', 'order' => true],
            ['name' => 'User', 'column' => 'user', 'order' => true],
            ['name' => 'Question', 'column' => 'question', 'order' => true],
            ['name' => 'Answer', 'column' => 'answer', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['event_id'],
            'headers' => [
                ['name' => 'Event id', 'column' => 'event_id'],
                ['name' => 'User id', 'column' => 'user_id'],
                ['name' => 'Question id', 'column' => 'question_id'],
                ['name' => 'Answer id', 'column' => 'answer_id'],
                ['name' => 'Point', 'column' => 'point'],
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
                'label' => 'Event id',
                'name' =>  'event_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('event_id', $id),
                'value' => (isset($edit)) ? $edit->event_id : ''
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
                'label' => 'Question id',
                'name' =>  'question_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('question_id', $id),
                'value' => (isset($edit)) ? $edit->question_id : ''
            ],
            [
                'type' => 'text',
                'label' => 'Answer id',
                'name' =>  'answer_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('answer_id', $id),
                'value' => (isset($edit)) ? $edit->answer_id : ''
            ],
            [
                'type' => 'text',
                'label' => 'Point',
                'name' =>  'point',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('point', $id),
                'value' => (isset($edit)) ? $edit->point : ''
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
            $filters[] = ['answer_participants.event_id', '=', request('event_id')];
        }
        if (request('user_id')) {
            $filters[] = ['answer_participants.user_id', '=', request('user_id')];
        }

        $dataQueries = AnswerParticipant::join('questions', 'questions.id', '=', 'answer_participants.question_id')
            ->join('users', 'users.id', '=', 'answer_participants.user_id')
            ->join('answers', 'answers.id', '=', 'answer_participants.answer_id')
            ->join('events', 'events.id', '=', 'answer_participants.event_id')
            ->join('workshops', 'workshops.id', '=', 'events.workshop_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('answer_participants.point', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('answers.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('questions.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
            });

        // Cek role user
        if (Auth::user()->role->name !== 'admin') {
            $dataQueries = $dataQueries->where('answer_participants.user_id', Auth::user()->id);
        }

        $dataQueries = $dataQueries
            ->select('answer_participants.*', 'workshops.name as workshop', 'answers.name as answer', 'users.name as user', 'questions.name as question')
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
        if (request('user_id')) {
            $params .= (empty($params) ? "?" : "&") . "user_id=" . request('user_id');
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
        $isEvent = Event::get();
        $isUser = Participant::get();

        $arrEvent = [];
        $arrEvent[] = ['value' => "", 'text' => "All Event"];
        foreach ($isEvent as $key => $event) {
            $arrEvent[] = ['value' => $event->id, 'text' => $event->workshop->name];
        }

        $arrUser = [];
        $arrUser[] = ['value' => "", 'text' => "All User"];
        foreach ($isUser as $key => $user) {
            $arrUser[] = ['value' => $user->id, 'text' => $user->name];
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
                [
                    'type' => 'select2',
                    'label' => 'User',
                    'name' => 'user_id',
                    'class' => 'col-md-2',
                    'options' => $arrUser,
                ],
            ];
        } else {
            $fields = [];
        }

        return $fields;
    }
}
