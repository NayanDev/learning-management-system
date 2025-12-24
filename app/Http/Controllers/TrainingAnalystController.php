<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\TrainingAnalyst;
use App\Models\TrainingAnalystData;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrainingAnalystController extends DefaultController
{
    protected $modelClass = TrainingAnalyst::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Training Analyst';
        $this->generalUri = 'training-analyst';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_approve', 'btn_access', 'btn_edit', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Training', 'column' => 'year', 'order' => true],
            ['name' => 'Qualification', 'column' => 'qualification', 'order' => true],
            ['name' => 'General', 'column' => 'general', 'order' => true],
            ['name' => 'Technic', 'column' => 'technic', 'order' => true],
            ['name' => 'User', 'column' => 'user', 'order' => true],
            ['name' => 'Divisi', 'column' => 'divisi', 'order' => true],
            ['name' => 'Status', 'column' => 'badge_status', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['training_id'],
            'headers' => [
                ['name' => 'Training id', 'column' => 'training_id'],
                ['name' => 'Qualification', 'column' => 'qualification'],
                ['name' => 'General', 'column' => 'general'],
                ['name' => 'Technic', 'column' => 'technic'],
                ['name' => 'Status', 'column' => 'status'],
                ['name' => 'Notes', 'column' => 'notes'],
                ['name' => 'Created date', 'column' => 'created_date'],
                ['name' => 'Approve by', 'column' => 'approve_by'],
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
                'type' => 'multiinput',
                'label' => 'Qualification',
                'method' => 'qualification',
                'enable_action' => false,
                'html_fields' => [
                    [
                        'name' => 'qualification',
                        'type' => 'onlyview',
                        'label' => 'Data',
                        'class' => 'col-md-12 my-2',
                        'value' => 'SMA'
                    ],
                    [
                        'name' => 'qualification',
                        'type' => 'onlyview',
                        'label' => 'Data',
                        'class' => 'col-md-12 my-2',
                        'value' => 'Bachelor'
                    ],
                    [
                        'name' => 'qualification',
                        'type' => 'onlyview',
                        'label' => 'Data',
                        'class' => 'col-md-12 my-2',
                        'value' => 'Sertifikasi'
                    ],
                    [
                        'name' => 'qualification',
                        'type' => 'onlyview',
                        'label' => 'Data',
                        'class' => 'col-md-12 my-2',
                        'value' => 'Magister'
                    ],
                    [
                        'name' => 'qualification',
                        'type' => 'onlyview',
                        'label' => 'Data',
                        'class' => 'col-md-12 my-2',
                        'value' => 'Doctoral'
                    ],
                    [
                        'name' => 'qualification',
                        'type' => 'onlyview',
                        'label' => 'Data',
                        'class' => 'col-md-12 my-2',
                        'value' => 'Professor'
                    ],
                ]
            ],
            [
                'type' => 'multiinput',
                'label' => 'General',
                'method' => 'general',
                'enable_action' => true,
                'html_fields' => [
                    [
                        'name' => 'general',
                        'type' => 'text',
                        'label' => 'Data',
                        'class' => 'col-md-10'
                    ]
                ]
            ],
            [
                'type' => 'multiinput',
                'label' => 'Technic',
                'method' => 'technic',
                'enable_action' => true,
                'html_fields' => [
                    [
                        'name' => 'technic',
                        'type' => 'text',
                        'label' => 'Data',
                        'class' => 'col-md-10'
                    ]
                ]
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
        $rules = $this->rules();

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

        try {
            $qualificationData = json_encode($request->qualification);
            $generalData = json_encode($request->general);
            $technicData = json_encode($request->technic);

            TrainingAnalyst::create([
                'training_id' => $request->input('training_id'),
                'qualification' => $qualificationData,
                'general'   => $generalData,
                'technic'   => $technicData,
                'user_id'   => Auth::user()->id,
                'divisi'   => $request->input('divisi'),
            ]);

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
        if (request('training_id')) {
            $filters[] = ['training_id', '=', request('training_id')];
        }

        $dataQueries = TrainingAnalyst::join('users', 'users.id', '=', 'training_analysts.user_id')
            ->join('trainings', 'trainings.id', '=', 'training_analysts.training_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('training_analysts.qualification', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('trainings.year', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_analysts.general', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_analysts.technic', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_analysts.divisi', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_analysts.status', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
            });

        // Cek role user
        if (Auth::user()->role->name !== 'admin') {
            $dataQueries = $dataQueries->where('training_analysts.user_id', Auth::user()->id);
        }

        $dataQueries = $dataQueries
            ->select('training_analysts.*', 'users.name as user', 'trainings.year as year')
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
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



    protected function trainingForm()
    {
        $queryString = request('training_analyst');
        $user = auth::user();

        if ($user->role !== 'admin') {
            $access = TrainingAnalyst::where('divisi', $user->divisi)
                ->where('id', $queryString)
                ->first();

            if (!$access) {
                abort(404);
            }
        }

        $permissions =  $this->arrPermissions;
        if ($this->dynamicPermission) {
            $permissions = (new Constant())->permissionByMenu($this->generalUri);
        }

        $trainingAnalyst = TrainingAnalyst::where('id', $queryString)->get();
        $analystData = TrainingAnalystData::where('training_analyst_id', $queryString)->get();

        $layout = 'custompage.training_analyst';
        $data['title'] = $this->title;
        $data['uri_key'] = $this->generalUri;
        $data['permissions'] = $permissions;
        $data['url_store'] = route($this->generalUri . '.store');
        $data['fields'] = $this->fields();
        $data['actionButtonViews'] = $this->actionButtonViews;
        $data['edit_fields'] = $this->fields('edit');
        $data['templateImportExcel'] = "#";

        $data['training_analyst'] = $trainingAnalyst;
        $data['analyst_data'] = $analystData;

        return view($layout, $data);
    }


    protected function saveAll(Request $request)
    {
        try {
            DB::beginTransaction();
            $trainingAnalystId = request('training_analyst_id');
            $existingIds = TrainingAnalystData::where('training_analyst_id', $trainingAnalystId)->pluck('id')->toArray();

            $processIds = [];

            foreach ($request->training_data as $index => $data) {
                $id = isset($existingIds[$index]) ? $existingIds[$index] : null;
                $training = TrainingAnalystData::updateOrCreate(
                    [
                        'id' => $id,
                        'training_analyst_id' => $trainingAnalystId
                    ],
                    [
                        'position' => $data['position'],
                        'personil' => $data['personil'],
                        'qualification' => json_encode($data['qualification']),
                        'general' => json_encode($data['general']),
                        'technic' => json_encode($data['technic'])
                    ]
                );
                $processIds[] = $training->id;
            }

            TrainingAnalystData::where('training_analyst_id', $trainingAnalystId)
                ->whereNotIn('id', $processIds)
                ->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan!'
            ]);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    protected function generatePDF()
    {
        $queryString = request('training_analyst');
        $user = Auth::user()->divisi;

        $access = TrainingAnalyst::where('divisi', $user)->find($queryString);
        if (!$access) {
            abort(404);
        }

        $trainingAnalyst = TrainingAnalyst::with(['training'])->where('id', $queryString)->first();
        $trainingAnalystData = TrainingAnalystData::where('training_analyst_id', $queryString)->get();

        $data = [
            'training_analyst' => $trainingAnalyst,
            'training_analyst_data' => $trainingAnalystData,
            'created' => TrainingAnalyst::with(['user', 'approver'])->find($queryString),
        ];

        $pdf = PDF::loadView('pdf.training_analyst', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->stream($this->title . '.pdf');
    }

    protected function approve(Request $request, $id)
    {
        $training = TrainingAnalyst::findOrFail($id);

        if ($request->status === 'submit') {
            $training->created_date = now();
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
}
