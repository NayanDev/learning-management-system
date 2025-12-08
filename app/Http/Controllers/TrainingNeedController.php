<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Models\Training;
use App\Models\TrainingNeed;
use Exception;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingNeedController extends DefaultController
{
    protected $modelClass = TrainingNeed::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $importExcelConfig;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;

    public function __construct()
    {
        $this->title = 'Training Need';
        $this->generalUri = 'training-need';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_approve', 'btn_access', 'btn_edit', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Year', 'column' => 'year', 'order' => true],
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
                ['name' => 'User id', 'column' => 'user_id'],
                ['name' => 'Divisi', 'column' => 'divisi'],
                ['name' => 'Approve by', 'column' => 'approve_by'],
                ['name' => 'Status', 'column' => 'status'],
                ['name' => 'Notes', 'column' => 'notes'],
                ['name' => 'Created date', 'column' => 'created_date'],
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
                'type' => 'onlyview',
                'label' => 'Divisi',
                'name' =>  'divisi',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('divisi', $id),
                'value' => (isset($edit)) ? $edit->divisi : Auth::user()->divisi
            ],
            [
                'type' => 'onlyview',
                'label' => 'Status',
                'name' =>  'status',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('status', $id),
                'value' => (isset($edit)) ? $edit->status : 'open'
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
                'label' => 'Approve by',
                'name' =>  'approve_by',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('approve_by', $id),
                'value' => (isset($edit)) ? $edit->approve_by : ''
            ],
            [
                'type' => 'hidden',
                'label' => 'Notes',
                'name' =>  'notes',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('notes', $id),
                'value' => (isset($edit)) ? $edit->notes : ''
            ],
            [
                'type' => 'hidden',
                'label' => 'Created date',
                'name' =>  'created_date',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('created_date', $id),
                'value' => (isset($edit)) ? $edit->created_date : ''
            ],
        ];

        $fields = array_merge([$fieldTraining], $fields);

        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
            'divisi' => 'required|string',
            'status' => 'required|string'
        ];

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

        $dataQueries = TrainingNeed::join('users', 'users.id', '=', 'training_needs.user_id')
            ->join('trainings', 'trainings.id', '=', 'training_needs.training_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('training_needs.divisi', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('trainings.year', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_needs.status', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
            });

        // Cek role user
        if (Auth::user()->role->name !== 'admin') {
            $dataQueries = $dataQueries->where('training_needs.user_id', Auth::user()->id);
        }

        $dataQueries = $dataQueries
            ->select('training_needs.*', 'users.name as user', 'trainings.year as year')
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


    public function approve(Request $request, $id)
    {
        $training = TrainingNeed::findOrFail($id);

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


    protected function generatePDF(Request $request)
    {
        try {
            $trainingNeeds = TrainingNeed::with([
                'training',
                'user',
                'approver',
                'workshops' => function ($query) {
                    $query->with(['workshop', 'participants.user']);
                },
                'participants.user'
            ])
                ->when($request->training_id, function ($query) use ($request) {
                    $query->where('id', $request->training_id);
                })
                ->get()
                ->map(function ($trainingNeed) {
                    // Transform workshops data
                    $workshopsData = $trainingNeed->workshops->map(function ($workshopItem) {
                        return [
                            'header' => [
                                'workshop_name' => $workshopItem->workshop->name,
                                // 'training_year' => $workshopItem->trainingNeed->training->year,
                                'instructor' => $workshopItem->instructor,
                                'start_date' => $workshopItem->start_date,
                                'end_date' => $workshopItem->end_date,
                                'position' => $workshopItem->position,
                                // 'created_by' => $workshopItem->trainingNeed->user->name,
                                'approved_by' => $workshopItem->trainingNeed->approver->name ?? 'Belum Disetujui',
                                // 'status' => $workshopItem->trainingNeed->status
                            ],
                            'participants' => $workshopItem->participants
                        ];
                    });
                    return $workshopsData;
                })
                ->flatten(1); // Flatten the collection to get all workshops in a single level

            if ($trainingNeeds->isEmpty()) {
                return response()->json(['message' => 'Data tidak ditemukan.'], 404);
            }

            $data = [
                'trainings' => $trainingNeeds,
                'created' => TrainingNeed::with(['user', 'approver'])->findOrFail($request->training_id),
                'year' => TrainingNeed::with(['training'])->findOrFail($request->training_id)
            ];
            // return dd($data);

            $pdf = PDF::loadView('pdf.training_need', $data)
                ->setPaper('A4', 'landscape');

            return $pdf->stream("Rencana_Training_" . date('Y-m-d') . ".pdf");
        } catch (Exception $e) {
            Log::error("Gagal generate PDF: " . $e->getMessage());
            return response()->json(['message' => 'Gagal generate PDF: ' . $e->getMessage()], 500);
        }
    }
}
