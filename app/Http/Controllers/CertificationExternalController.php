<?php

namespace App\Http\Controllers;

use App\Models\CertificationExternal;
use App\Models\Event;
use App\Models\Participant;
use App\Models\TemplateCertification;
use Exception;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CertificationExternalController extends DefaultController
{
    protected $modelClass = CertificationExternal::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['print', 'list','show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Certification External';
        $this->generalUri = 'certification-external';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_print', 'btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Participant', 'column' => 'participant_name', 'order' => true],
                    ['name' => 'Event', 'column' => 'workshop_name', 'order' => true],
                    ['name' => 'File path', 'column' => 'file_path', 'order' => true],
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['participant_id'],
            'headers' => [
                    ['name' => 'Participant id', 'column' => 'participant_id'],
                    ['name' => 'Event id', 'column' => 'event_id'],
                    ['name' => 'File path', 'column' => 'file_path'], 
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

        $trainingNeeds = Event::with(['workshop'])->get();
        $training = $trainingNeeds->map(function ($item) {
            return [
                'value' => $item->id,
                'text' => ($item->workshop->name ?? '-')
            ];
        })->toArray();

        $participantData = Participant::with(['event'])->get();
        $participantMap = $participantData->map(function ($item) {
            return [
                'value' => $item->id,
                'text' => $item->name . ' (' . $item->event->workshop->name . ')'
            ];
        })->toArray();

        $fields = [
                    [
                        'type' => 'select2',
                        'label' => 'Participant',
                        'name' =>  'participant_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('participant_id', $id),
                        'value' => (isset($edit)) ? $edit->participant_id : '',
                        'options' => $participantMap,
                    ],
                    [
                        'type' => 'select2',
                        'label' => 'Event id',
                        'name' =>  'event_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('event_id', $id),
                        'value' => (isset($edit)) ? $edit->event_id : '',
                        'options' => $training,
                    ],
                    [
                        'type' => 'upload',
                        'label' => 'File path',
                        'name' =>  'file_path',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('file_path', $id),
                        'value' => (isset($edit)) ? $edit->file_path : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
        ];

        return $rules;
    }


    protected function store(Request $request)
    {
        $rules = $this->rules();

        $certificationPath = public_path('images/certification_external');
        if (!file_exists($certificationPath)) {
            mkdir($certificationPath, 0775, true);
        }

        $template = $request->file('file_path');
        if ($template) {
            $fileName = time() . '_' . $template->getClientOriginalName();
            $template->move($certificationPath, $fileName);
        } else {
            $fileName = null;
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messageErrors = (new Validation)->modify($validator, $rules);
            return response()->json([
                'status' => false,
                'alert' => 'danger',
                'message' => 'Required Form',
                'validation_errors' => $messageErrors,
            ], 200);
        }

        DB::beginTransaction();

        try {
            $insert = new CertificationExternal();
            $insert->participant_id = $request->participant_id;
            $insert->event_id = $request->event_id;
            $insert->file_path = $fileName;
            $insert->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Was Created Successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
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
            $filters[] = ['certification_externals.event_id', '=', request('event_id')];
        }

        $dataQueries = CertificationExternal::join('participants', 'participants.id', '=', 'certification_externals.participant_id')
            ->join('events', 'events.id', '=', 'certification_externals.event_id')
            ->join('workshops', 'workshops.id', '=', 'events.workshop_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('certification_externals.file_path', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('participants.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
            });

        $dataQueries = $dataQueries
            ->select('certification_externals.*', 'participants.name as participant_name', 'workshops.name as workshop_name')
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
