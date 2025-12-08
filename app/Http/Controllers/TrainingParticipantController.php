<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\TrainingParticipant;
use App\Models\TrainingWorkshop;
use Exception;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TrainingParticipantController extends DefaultController
{
    protected $modelClass = TrainingParticipant::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['participant-ajax', 'list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Training Participant';
        $this->generalUri = 'training-participant';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Training', 'column' => 'workshop', 'order' => true],
            ['name' => 'Name', 'column' => 'name', 'order' => true],
            ['name' => 'Email', 'column' => 'email', 'order' => true],
            ['name' => 'Divisi', 'column' => 'divisi', 'order' => true],
            ['name' => 'Nik', 'column' => 'nik', 'order' => true],
            ['name' => 'Unit kerja', 'column' => 'unit_kerja', 'order' => true],
            ['name' => 'User', 'column' => 'user', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['company'],
            'headers' => [
                ['name' => 'Company', 'column' => 'company'],
                ['name' => 'Nik', 'column' => 'nik'],
                ['name' => 'Name', 'column' => 'name'],
                ['name' => 'Divisi', 'column' => 'divisi'],
                ['name' => 'Unit kerja', 'column' => 'unit_kerja'],
                ['name' => 'Status', 'column' => 'status'],
                ['name' => 'Jk', 'column' => 'jk'],
                ['name' => 'Email', 'column' => 'email'],
                ['name' => 'Telp', 'column' => 'telp'],
                ['name' => 'Training need workshop id', 'column' => 'training_need_workshop_id'],
            ]
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $trainingWorkshop = request('training_workshop');
        if ($trainingWorkshop) {
            $fieldTraining =
                [
                    'type' => 'hidden',
                    'label' => 'Training need workshop id',
                    'name' =>  'training_need_workshop_id',
                    'class' => 'col-md-12 my-2',
                    'required' => $this->flagRules('training_need_workshop_id', $id),
                    'value' => (isset($edit)) ? $edit->training_need_workshop_id : $trainingWorkshop
                ];
        } else {
            $trainingNeeds = TrainingWorkshop::with(['workshop'])->get();
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
                    'name' =>  'training_need_workshop_id',
                    'class' => 'col-md-12 my-2',
                    'required' => 'required',
                    'value' => (isset($edit)) ? $edit->training_need_workshop_id : '',
                    'options' => $training,
                ];
        }

        $fields = [
            [
                'type' => 'participant',
                'label' => 'Participant',
                'name' => 'participant',
                'class' => 'col-md-12 my-2',
                'key' => 'nama',
                'ajaxUrl' => url('participant-ajax'),
                'table_headers' => ['Name']
            ],
            [
                'type' => 'hidden',
                'label' => 'User id',
                'name' =>  'user_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('user_id', $id),
                'value' => (isset($edit)) ? $edit->user_id : Auth::user()->id
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
        if (request('training_workshop')) {
            $params = "?training_workshop=" . request('training_workshop');
        }

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
        $data['uri_list_api'] = route($this->generalUri . '.listapi') . $params;
        $data['uri_create'] = route($this->generalUri . '.create');
        $data['url_store'] = route($this->generalUri . '.store');
        $data['fields'] = $this->fields();
        $data['edit_fields'] = $this->fields('edit');
        $data['actionButtonViews'] = $this->actionButtonViews;
        $data['templateImportExcel'] = "#";
        $data['import_scripts'] = $this->importScripts;
        $data['import_styles'] = $this->importStyles;
        $data['filters'] = $this->filters();

        return view($layout, $data);
    }


    protected function getFilteredApiData()
    {
        try {
            $response = Http::acceptJson()->get('https://simco.sampharindogroup.com/api/pegawai');

            if ($response->successful()) {
                $apiEmployees = $response->json();

                if (!is_array($apiEmployees)) {
                    return ['data' => [], 'total' => 0];
                }

                // Ambil parameter search
                $search = request()->get('search', null);

                // Jika ada search, filter data manual
                if ($search && strlen($search) > 3) {
                    $apiEmployees = array_filter($apiEmployees, function ($item) use ($search) {
                        return stripos($item['nama'], $search) !== false
                            || stripos($item['divisi'], $search) !== false
                            || stripos($item['unit_kerja'], $search) !== false
                            || stripos($item['nik'], $search) !== false;
                    });
                    // reset index array setelah filter
                    $apiEmployees = array_values($apiEmployees);
                }

                $limit = (int) request()->get('length', 10);
                $start = (int) request()->get('start', 0);
                $page = max(1, (int) (($start / $limit) + 1));

                $totalRecords = count($apiEmployees);
                $lastPage = (int) ceil($totalRecords / $limit);

                // Pagination slice dari data yang sudah difilter
                $paginatedData = array_slice($apiEmployees, $start, $limit);

                // URL dasar untuk pagination (sesuaikan URL endpointmu)
                $baseUrl = url()->current();

                // Fungsi untuk generate URL halaman dengan include search parameter
                $getPageUrl = function ($pageNum) use ($baseUrl, $limit, $search) {
                    if ($pageNum <= 0) return null;

                    $params = [
                        'start' => ($pageNum - 1) * $limit,
                        'length' => $limit
                    ];

                    // ← Tambahkan search parameter jika ada
                    if ($search) {
                        $params['search'] = $search;
                    }

                    return $baseUrl . '?' . http_build_query($params);
                };

                return [
                    'data' => $paginatedData,
                    'total' => $totalRecords,
                    'per_page' => $limit,
                    'current_page' => $page,
                    'last_page' => $lastPage,
                    'first_page_url' => $getPageUrl(1),
                    'last_page_url' => $getPageUrl($lastPage),
                    'next_page_url' => $page < $lastPage ? $getPageUrl($page + 1) : null,
                    'prev_page_url' => $page > 1 ? $getPageUrl($page - 1) : null,
                    'path' => $baseUrl,
                    'from' => $totalRecords > 0 ? $start + 1 : 0,
                    'to' => min($start + $limit, $totalRecords),
                    'links' => collect(range(1, $lastPage))->map(function ($pageNum) use ($page, $getPageUrl) {
                        return [
                            'url' => $getPageUrl($pageNum),
                            'label' => (string) $pageNum,
                            'active' => $pageNum === $page,
                        ];
                    })->toArray(),
                ];
            }
        } catch (Exception $e) {
            Log::error("Gagal mengambil data API: " . $e->getMessage());
        }
        return ['data' => [], 'total' => 0];
    }


    protected function participantAjax()
    {
        $dataQueries = $this->getFilteredApiData();
        if (!is_array($dataQueries)) {
            $dataQueries = json_decode($dataQueries, true);
        }

        $data['header'] = ['nama'];
        $data['body'] = $dataQueries;

        return $data;
    }


    protected function store(Request $request)
    {
        try {
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

            DB::beginTransaction();

            // Decode JSON string dari input hidden
            $selectedParticipants = json_decode($request->participant, true);

            if (empty($selectedParticipants)) {
                throw new Exception('Tidak ada peserta yang dipilih');
            }

            // Debug log
            Log::info('Selected Participants:', ['participants' => $selectedParticipants]);

            // Simpan data participant dengan semua field
            foreach ($selectedParticipants as $participant) {
                $insert = new TrainingParticipant();

                $insert->training_need_workshop_id = $request->training_need_workshop_id;
                if (is_array($participant)) {
                    // Jika sudah berupa array dengan data lengkap
                    $insert->company = $participant['company'] ?? '';
                    $insert->nik = $participant['nik'] ?? '';
                    $insert->name = $participant['nama'] ?? $participant['name'] ?? '';
                    $insert->divisi = $participant['divisi'] ?? '';
                    $insert->unit_kerja = $participant['unit_kerja'] ?? '';
                    $insert->status = $participant['status'] ?? '';
                    $insert->jk = $participant['jk'] ?? '';
                    $insert->email = $participant['email'] ?? '';
                    $insert->telp = $participant['telp'] ?? '';
                } else {
                    // Jika hanya string nama (fallback untuk kompatibilitas)
                    $insert->company = '';
                    $insert->nik = '';
                    $insert->name = $participant;
                    $insert->divisi = '';
                    $insert->unit_kerja = '';
                    $insert->status = '';
                    $insert->jk = '';
                    $insert->email = '';
                    $insert->telp = '';
                }
                $insert->training_need_workshop_id = $request->training_need_workshop_id;
                $insert->user_id = $request->user_id;
                $insert->save();
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data berhasil disimpan',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error storing training needs: ' . $e->getMessage());

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
        if (request('training_workshop')) {
            $filters[] = ['training_need_workshop_id', '=', request('training_workshop')];
        }

        $dataQueries = TrainingParticipant::join('training_need_workshops', 'training_need_workshops.id', '=', 'training_need_participants.training_need_workshop_id')
            ->join('workshops', 'workshops.id', '=', 'training_need_workshops.workshop_id')
            ->join('users', 'users.id', '=', 'training_need_participants.user_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('training_need_participants.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_need_participants.email', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_need_participants.divisi', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('training_need_participants.unit_kerja', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
            });

        $dataQueries = $dataQueries
            ->select('training_need_participants.*', 'workshops.name as workshop', 'users.name as user')
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }
}
