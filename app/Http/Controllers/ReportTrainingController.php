<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ReportTraining;
use App\Services\DirectorSignatureService;
use App\Services\SignatureService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportTrainingController extends DefaultController
{
    protected $modelClass = ReportTraining::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Report Training';
        $this->generalUri = 'report-training';
        $this->arrPermissions = ['signature', 'pdf', 'list','show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
        $this->actionButtons = ['btn_signature', 'btn_pdf', 'btn_edit', 'btn_show', 'btn_delete'];
        $this->actionButtonViews = [
            'backend.idev.buttons.approve',
            'backend.idev.buttons.director_signature',
        ];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => 'id', 'order' => true],
                    ['name' => 'Event', 'column' => 'workshop_name', 'order' => true],
                    ['name' => 'Category training', 'column' => 'category_training', 'order' => true],
                    ['name' => 'Description', 'column' => 'description', 'order' => true],
                    ['name' => 'Materi', 'column' => 'materi', 'order' => true],
                    ['name' => 'Targets', 'column' => 'targets', 'order' => true],
                    ['name' => 'Notes', 'column' => 'notes', 'order' => true],
                    ['name' => 'Report date', 'column' => 'report_date', 'order' => true],
                    ['name' => 'Manager', 'column' => 'manager_name', 'order' => true],
                    ['name' => 'Director', 'column' => 'director_name', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['event_id'],
            'headers' => [
                    ['name' => 'Event', 'column' => 'workshop_name'],
                    ['name' => 'Category training', 'column' => 'category_training'],
                    ['name' => 'Description', 'column' => 'description'],
                    ['name' => 'Materi', 'column' => 'materi'],
                    ['name' => 'Targets', 'column' => 'targets'],
                    ['name' => 'Notes', 'column' => 'notes'],
                    ['name' => 'Report date', 'column' => 'report_date'],
                    ['name' => 'Manager', 'column' => 'manager_name'],
                    ['name' => 'Director', 'column' => 'director_name'], 
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

        $instructor = [
            ['value' => 'internal', 'text' => 'Internal'],
            ['value' => 'external', 'text' => 'External'],
        ];

        $manager = 180;
        $director = 1;

        $fields = [
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
                        'type' => 'select',
                        'label' => 'Category training',
                        'name' =>  'category_training',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('category_training', $id),
                        'value' => (isset($edit)) ? $edit->category_training : '',
                        'options' => $instructor,
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
                        'type' => 'textarea',
                        'label' => 'Materi',
                        'name' =>  'materi',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('materi', $id),
                        'value' => (isset($edit)) ? $edit->materi : ''
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Targets',
                        'name' =>  'targets',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('targets', $id),
                        'value' => (isset($edit)) ? $edit->targets : 'Mampu mengaplikasikan hasil pelatihan yang telah diikuti dengan baik dan benar.'
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Notes',
                        'name' =>  'notes',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('notes', $id),
                        'value' => (isset($edit)) ? $edit->notes : 'Materi pelatihan, Resume pelatihan, Lampiran.'
                    ],
                    [
                        'type' => 'datetime',
                        'label' => 'Report date',
                        'name' =>  'report_date',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('report_date', $id),
                        'value' => (isset($edit)) ? $edit->report_date : now()
                    ],
                    [
                        'type' => 'onlyview',
                        'label' => 'Manager',
                        'name' =>  'manager',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('manager', $id),
                        'value' => (isset($edit)) ? $edit->manager : $manager,
                    ],
                    [
                        'type' => 'onlyview',
                        'label' => 'Director Name',
                        'name' =>  'director_name',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('director_name', $id),
                        'value' => (isset($edit)) ? $edit->director_name : 'MAKMURI YUSIN'
                    ],
                    [
                        'type' => 'onlyview',
                        'label' => 'Director',
                        'name' =>  'director',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('director', $id),
                        'value' => (isset($edit)) ? $edit->director : $director,
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'event_id' => 'required|string',
                    'category_training' => 'required|string',
                    'description' => 'required|string',
                    'materi' => 'required|string',
                    'targets' => 'required|string',
                    'notes' => 'required|string',
                    'report_date' => 'required|string',
                    'manager' => 'required|string',
                    'director' => 'required|string',
        ];

        return $rules;
    }


    public function generatePDF(ReportTraining $report)
    {
        // Load relasi
        $report->load([
            'managerUser',
            'event.documentations',
            'event.materials',
            'event.questions',
            'event.evaluations',
        ]);

        $event = $report->event;

        if(!$event) {
            abort(404, 'Event not found for this report.');
        }

        $data = [
            'report' => $report,
            'event' => $event,
            'documentations' => $event->documentations,
            'materials' => $event->materials,
            'questions' => $event->questions,
            'evaluations' => $event->evaluations,
        ];

        $pdf = Pdf::loadView('pdf.report_training', $data)
                    ->setPaper('a4', 'portrait');

        $fileName = 'Report_' . Str::slug($event->name ?? 'event') . '_' . now()->year . '.pdf';
        return $pdf->stream($fileName);
    }


    public function signatureExternal(Request $request)
    {
        $reportId = $request->query('report_id');
        abort_if(empty($reportId), 404);

        $report = ReportTraining::with(['event.workshop', 'managerUser'])->findOrFail($reportId);
        $pendingReports = ReportTraining::with(['event.workshop'])
            ->whereNull('director_signature')
            ->orderByDesc('report_date')
            ->get();

        return view('frontend.signaturepad-direksi', compact('report', 'pendingReports'));
    }


    public function storeSignature(Request $request)
    {
        $validated = $request->validate([
            'report_id' => ['required', 'integer', 'exists:report_trainings,id'],
            'signature_svg' => ['required', 'string'],
        ]);

        $report = ReportTraining::findOrFail($validated['report_id']);
        $signatureSvg = trim($validated['signature_svg']);

        if (! str_starts_with($signatureSvg, '<svg')) {
            return back()
                ->withInput()
                ->with('error', 'Format tanda tangan tidak valid.');
        }

        // Tentukan path folder public/report_signature
        $signaturePath = public_path('report_signature');
        
        // Buat folder jika belum ada
        if (!is_dir($signaturePath)) {
            mkdir($signaturePath, 0755, true);
        }

        // Generate nama file SVG: signature_[timestamp]_[uniqid].svg
        $signatureFileName = 'signature_' . time() . '_' . uniqid() . '.svg';
        $filePath = $signaturePath . '/' . $signatureFileName;

        // Simpan SVG ke file
        if (file_put_contents($filePath, $signatureSvg) === false) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan file tanda tangan.');
        }

        // Hapus file lama jika ada
        if ($report->director_signature && file_exists($signaturePath . '/' . $report->director_signature)) {
            @unlink($signaturePath . '/' . $report->director_signature);
        }

        // Simpan nama file ke database
        $report->director_signature = $signatureFileName;
        $report->save();

        return redirect()
            ->route('director.signature.external', ['report_id' => $report->id])
            ->with('success', 'Tanda tangan berhasil disimpan dalam format SVG.');
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
            $filters[] = ['report_trainings.event_id', '=', request('event_id')];
        }

        $dataQueries = ReportTraining::join('events', 'events.id', '=', 'report_trainings.event_id')
            ->join('workshops', 'workshops.id', '=', 'events.workshop_id')
            ->join('users', 'users.id', '=', 'report_trainings.manager',)
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('workshops.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('report_trainings.category_training', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('report_trainings.description', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('report_trainings.materi', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('report_trainings.targets', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('report_trainings.notes', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('report_trainings.report_date', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('report_trainings.director_name', 'LIKE', '%' . $orThose . '%');
            });

        $dataQueries = $dataQueries
            ->select('report_trainings.*', 'workshops.name as workshop_name', 'users.name as manager_name')
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


    protected function approve(Request $request, $id, SignatureService $signatureService, DirectorSignatureService $directorSignatureService)
    {
        try {
            DB::beginTransaction();

            $trainingReport = ReportTraining::findOrFail($id);

            if ($request->status === 'approve') {
                $trainingReport->manager = $request->approve_by;
                $trainingReport->report_date = now();
            }

            $trainingReport->status = $request->status;
            $trainingReport->notes = $request->notes ?: '-';
            $trainingReport->updated_at = now();
            $trainingReport->save();

            if ($request->status === 'close' && $request->filled('director_signature')) {
                $directorSignatureService->create(
                    $trainingReport,
                    $request->director_signature
                );
            } elseif ($request->status !== 'close') {
                $signatureService->create($trainingReport);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Status updated successfully'
            ]);

        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

}
