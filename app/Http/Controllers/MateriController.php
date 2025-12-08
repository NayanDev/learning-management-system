<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Materi;
use Exception;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MateriController extends DefaultController
{
    protected $modelClass = Materi::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Materi';
        $this->generalUri = 'materi';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Training', 'column' => 'workshop', 'order' => true],
            ['name' => 'File path', 'column' => 'file_path', 'order' => true],
            ['name' => 'File type', 'column' => 'file_type', 'order' => true],
            ['name' => 'Id youtube', 'column' => 'id_youtube', 'order' => true],
            ['name' => 'Description', 'column' => 'description', 'order' => true],
            ['name' => 'User', 'column' => 'user', 'order' => true],
            ['name' => 'Divisi', 'column' => 'divisi', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['event_id'],
            'headers' => [
                ['name' => 'Event id', 'column' => 'event_id'],
                ['name' => 'File path', 'column' => 'file_path'],
                ['name' => 'File type', 'column' => 'file_type'],
                ['name' => 'Id youtube', 'column' => 'id_youtube'],
                ['name' => 'Description', 'column' => 'description'],
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

        $event = request('event_id');
        if ($event) {
            $fieldTraining =
                [
                    'type' => 'hidden',
                    'label' => 'Event id',
                    'name' =>  'event_id',
                    'class' => 'col-md-12 my-2',
                    'required' => $this->flagRules('event_id', $id),
                    'value' => (isset($edit)) ? $edit->event_id : $event
                ];
        } else {
            $trainingNeeds = Event::with(['workshop'])->get();
            $training = $trainingNeeds->map(function ($item) {
                return [
                    'value' => $item->id,
                    'text' => ($item->workshop->name ?? '-')
                ];
            })->toArray();

            $fieldTraining =
                [
                    'type' => 'select',
                    'label' => 'Training',
                    'name' =>  'event_id',
                    'class' => 'col-md-12 my-2',
                    'required' => 'required',
                    'value' => (isset($edit)) ? $edit->event_id : '',
                    'options' => $training,
                ];
        }

        $fields = [
            [
                'type' => 'upload',
                'label' => 'Upload Materi',
                'name' =>  'file',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->file_path : '',
                'accept' => '.pdf,.ppt,.pptx',
            ],
            [
                'type' => 'text',
                'label' => 'ID youtube (optional)',
                'name' =>  'id_youtube',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('id_youtube', $id),
                'value' => (isset($edit)) ? $edit->id_youtube : ''
            ],
            [
                'type' => 'textarea',
                'label' => 'Description',
                'name' =>  'description',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('description', $id),
                'value' => (isset($edit)) ? $edit->description : '-'
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
        $rules = [];

        return $rules;
    }

    protected function store(Request $request)
    {
        $request->validate([
            'file' => 'file|mimes:pdf,ppt,pptx|max:10240',
        ]);

        $file = $request->file('file');

        if ($file) {
            $originalName = $file->getClientOriginalName();
            $fileNameOnly = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $path = $file->storeAs('public/materi', $fileNameOnly . '_' . time() . '.' . $extension);
        } else {
            $originalName = '';
            $fileNameOnly = '';
            $extension = '';
            $path = '';
        }


        DB::beginTransaction();

        try {
            $insert = new Materi();
            $insert->description = $request->description;
            $insert->event_id = $request->event_id;
            $insert->user_id = $request->user_id;
            $insert->divisi = $request->divisi;
            $insert->file_type = $extension;
            $insert->file_path = str_replace('public/', 'storage/', $path);
            $insert->id_youtube = $request->id_youtube;
            $insert->save();

            $this->afterMainInsert($insert, $request);

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
            $filters[] = ['event_id', '=', request('event_id')];
        }

        $dataQueries = Materi::join('events', 'events.id', '=', 'materis.event_id')
            ->join('users', 'users.id', '=', 'materis.user_id')
            ->join('workshops', 'workshops.id', '=', 'events.workshop_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('materis.file_path', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('materis.file_type', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('materis.id_youtube', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('materis.description', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('materis.divisi', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
            });

        // Cek role user
        if (Auth::user()->role->name !== 'admin') {
            $dataQueries = $dataQueries->where('materis.user_id', Auth::user()->id);
        }

        $dataQueries = $dataQueries
            ->select('materis.*', 'workshops.name as workshop', 'users.name as user')
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
