<?php

namespace App\Http\Controllers;

use App\Models\ResumeMateri;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ResumeMateriController extends DefaultController
{
    protected $modelClass = ResumeMateri::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['print', 'list','show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Resume Materi';
        $this->generalUri = 'resume-materi';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_print', 'btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Event', 'column' => 'workshop_name', 'order' => true],
                    ['name' => 'File path', 'column' => 'file_path', 'order' => true],
                    ['name' => 'User', 'column' => 'user_name', 'order' => true],
                    ['name' => 'Divisi', 'column' => 'divisi', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['event_id'],
            'headers' => [
                    ['name' => 'Event id', 'column' => 'event_id'],
                    ['name' => 'File path', 'column' => 'file_path'],
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
                        'type' => 'upload',
                        'label' => 'File path',
                        'name' =>  'file_path',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('file_path', $id),
                        'value' => (isset($edit)) ? $edit->file_path : ''
                    ],
                    [
                        'type' => 'hidden',
                        'label' => 'Event ID',
                        'name' =>  'event_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('event_id', $id),
                        'value' => (isset($edit)) ? $edit->event_id : request('event_id'),
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'file_path' => 'required|string',
        ];

        return $rules;
    }

    
    protected function store(Request $request)
    {
        $rules = array_merge($this->rules(), [
            'file_path' => 'required|file|mimes:pdf|max:5120', // max 5MB
        ]);

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

        $beforeInsertResponse = $this->beforeMainInsert($request);
        if ($beforeInsertResponse !== null) {
            return $beforeInsertResponse;
        }

        DB::beginTransaction();

        try {

            $appendStore = $this->appendStore($request);

            if (array_key_exists('error', $appendStore)) {
                return response()->json($appendStore['error'], 200);
            }

            // Folder tujuan
            $destinationPath = public_path('resume_external');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Upload PDF
            $fileName = null;

            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');

                $fileName = time() . '_' . uniqid() . '.pdf';

                $file->move($destinationPath, $fileName);
            }

            $insert = new $this->modelClass();

            $insert->event_id = $request->event_id;
            $insert->file_path = $fileName;
            $insert->user_id = Auth::id();
            $insert->divisi = Auth::user()->divisi;

            if (array_key_exists('columns', $appendStore)) {
                foreach ($appendStore['columns'] as $as) {
                    $insert->{$as['name']} = $as['value'];
                }
            }

            $insert->save();

            $this->afterMainInsert($insert, $request);

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Was Created Successfully',
                'data' => $insert
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'alert' => 'danger',
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
            $filters[] = ['resume_materies.event_id', '=', request('event_id')];
        }

        $dataQueries = ResumeMateri::join('events', 'events.id', '=', 'resume_materies.event_id')
            ->join('workshops', 'workshops.id', '=', 'events.workshop_id',)
            ->join('users', 'users.id', '=', 'resume_materies.user_id',)
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('resume_materies.file_path', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('resume_materies.divisi', 'LIKE', '%' . $orThose . '%');
            });

            if (Auth::user()->role->name !== 'admin') {
                $dataQueries = $dataQueries->whereHas('participant', function ($query) {
                    $query->where('nik', Auth::user()->nik);
                });
            }

        $dataQueries = $dataQueries
            ->select('resume_materies.*', 'workshops.name as workshop_name', 'users.name as user_name')
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
