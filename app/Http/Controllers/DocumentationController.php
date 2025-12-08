<?php

namespace App\Http\Controllers;

use App\Models\Documentation;
use App\Models\Event;
use Exception;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Idev\EasyAdmin\app\Helpers\Constant;
use Illuminate\Support\Facades\Validator;

class DocumentationController extends DefaultController
{
    protected $modelClass = Documentation::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Documentation';
        $this->generalUri = 'documentation';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Name', 'column' => 'name', 'order' => true],
            ['name' => 'Image', 'column' => 'view_image', 'order' => true],
            ['name' => 'Description', 'column' => 'description', 'order' => true],
            ['name' => 'Event id', 'column' => 'workshop', 'order' => true],
            ['name' => 'User id', 'column' => 'user', 'order' => true],
            ['name' => 'Divisi', 'column' => 'divisi', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['name'],
            'headers' => [
                ['name' => 'Name', 'column' => 'name'],
                ['name' => 'Image', 'column' => 'image'],
                ['name' => 'Description', 'column' => 'description'],
                ['name' => 'Event id', 'column' => 'event_id'],
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
                'type' => 'text',
                'label' => 'Title',
                'name' =>  'name',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('name', $id),
                'value' => (isset($edit)) ? $edit->name : ''
            ],
            [
                'type' => 'upload',
                'label' => 'Image',
                'name' =>  'image',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('image', $id),
                'value' => (isset($edit)) ? $edit->image : ''
            ],
            [
                'type' => 'textarea',
                'label' => 'Description',
                'name' =>  'description',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('description', $id),
                'value' => (isset($edit)) ? $edit->description : ''
            ],
            [
                'type' => 'hidden',
                'label' => 'User id',
                'name' =>  'user_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('user_id', $id),
                'value' => (isset($edit)) ? $edit->user_id : Auth::user()->id,
            ],
            [
                'type' => 'hidden',
                'label' => 'Divisi',
                'name' =>  'divisi',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('divisi', $id),
                'value' => (isset($edit)) ? $edit->divisi : Auth::user()->divisi,
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
        $name = $request->name;
        $description = $request->description;
        $eventId = $request->event_id;
        $userId = $request->user_id;
        $divisi =  $request->divisi;

        $folder = public_path('images/documentation');
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $file = $request->file('image');
        if ($file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($folder, $fileName);
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
            $insert = new Documentation();
            $insert->name = $name;
            $insert->image = $fileName;
            $insert->description = $description;
            $insert->event_id = $eventId;
            $insert->user_id = $userId;
            $insert->divisi = $divisi;
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
            $filters[] = ['documentations.event_id', '=', request('event_id')];
        }

        $dataQueries = Documentation::join('events', 'events.id', '=', 'documentations.event_id')
            ->join('workshops', 'workshops.id', '=', 'events.workshop_id')
            ->join('users', 'users.id', '=', 'documentations.user_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('documentations.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('documentations.divisi', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
            });

        // Cek role user
        if (Auth::user()->role->name !== 'admin') {
            $dataQueries = $dataQueries->where('documentations.user_id', Auth::user()->id);
        }

        $dataQueries = $dataQueries
            ->select('documentations.*', 'workshops.name as workshop', 'users.name as user')
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }


    public function update(Request $request, $id)
    {
        $rules = $this->rules();
        $name = $request->name;
        $description = $request->description;
        $eventId = $request->event_id;
        $userId = $request->user_id;
        $divisi =  $request->divisi;

        $folder = public_path('images/documentation');
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $file = $request->file('image');
        if ($file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($folder, $fileName);
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
            $update = Documentation::findOrFail($id);
            $update->name = $name;
            $update->image = $fileName;
            $update->description = $description;
            $update->event_id = $eventId;
            $update->user_id = $userId;
            $update->divisi = $divisi;
            $update->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Was Updated Successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    protected function generatePDF()
    {
        $eventId = request('event_id');
        if (!$eventId) {
            abort(403, 'Event Not Found.');
        }
        $documentation = Documentation::where('event_id', $eventId)->get();

        $data = [
            'documentations' => $documentation,
        ];

        $pdf = PDF::loadView('pdf.documentation', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->stream("Certification" . ($request->year ?? date('Y')) . ".pdf");
    }


    protected function exportPdf()
    {
        $dataQueries = $this->defaultDataQuery()->take(1000)->get();

        $datas['title'] = $this->title;
        $datas['enable_number'] = true;
        $datas['data_headers'] = $this->tableHeaders;
        $datas['data_queries'] = $dataQueries;
        $datas['exclude_columns'] = ['id', '#'];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('easyadmin::pdf.default', $datas);

        return $pdf->stream($this->title . '.pdf');
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

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'backend.idev.list_drawer_documentation';

        return view($layout, $data);
    }
}
