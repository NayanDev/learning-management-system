<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Str;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Idev\EasyAdmin\app\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ParticipantController extends DefaultController
{
    protected $modelClass = Participant::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Participant';
        $this->generalUri = 'participant';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Training', 'column' => 'workshop', 'order' => true],
            ['name' => 'Name', 'column' => 'name', 'order' => true],
            ['name' => 'Divisi', 'column' => 'divisi', 'order' => true],
            ['name' => 'Unit kerja', 'column' => 'unit_kerja', 'order' => true],
            ['name' => 'Email', 'column' => 'email', 'order' => true],
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
                ['name' => 'Event id', 'column' => 'event_id'],
                ['name' => 'User id', 'column' => 'user_id'],
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
            $filters[] = ['participants.event_id', '=', request('event_id')];
        }

        $dataQueries = Participant::join('events', 'events.id', '=', 'participants.event_id')
            ->join('workshops', 'workshops.id', '=', 'events.workshop_id')
            ->join('users', 'users.id', '=', 'participants.user_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('participants.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('participants.email', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('participants.divisi', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('participants.unit_kerja', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
            });

        // Cek role user
        if (Auth::user()->role->name !== 'admin') {
            $dataQueries = $dataQueries->where('participants.user_id', Auth::user()->id);
        }

        $dataQueries = $dataQueries
            ->select('participants.*', 'workshops.name as workshop', 'users.name as user')
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

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'backend.idev.list_drawer_participant';

        return view($layout, $data);
    }


    public function generateUser($event_id)
    {
        try {
            // Ambil data event & peserta
            $participants = Participant::where('event_id', $event_id)->get();
            $event = Event::findOrFail($event_id);

            $created = 0;
            $skipped = 0;

            foreach ($participants as $p) {
                // Cek jika email kosong, langsung skip
                if (!$p->email) {
                    $skipped++;
                    continue;
                }

                // Cek apakah email sudah terdaftar
                $exists = User::where('nik', $p->nik)
                    ->orWhere('email', $p->email)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $slug = Str::slug($p->name);

                // Buat user baru
                User::create([
                    'name'       => $p->name,
                    'email'      => $slug,
                    'company'    => $p->company,
                    'divisi'     => $p->divisi,
                    'unit_kerja' => $p->unit_kerja,
                    'status'     => $p->status,
                    'jk'         => $p->jk,
                    'telp'       => $p->telp,
                    'nik'        => $p->nik,
                    'role_id'    => Role::where('name', 'participant')->first()->id,
                    'password'   => bcrypt('pelatihan' . $event->year),
                ]);

                $created++;
            }

            // Jika dipanggil dari fetch(), kembalikan JSON
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'created' => $created,
                    'skipped' => $skipped,
                ]);
            }

            // Jika dipanggil dari form biasa
            return back()->with('success', "✅ User berhasil dibuat: $created | ⏩ Dilewati: $skipped");
        } catch (Exception $e) {
            // Log error ke laravel.log
            Log::error('Generate User Error: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', '❌ Gagal generate user: ' . $e->getMessage());
        }
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
                $insert = new Participant();

                $insert->event_id = $request->event_id;
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
                $insert->event_id = $request->event_id;
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


    protected function participantAccount()
    {
        $accountId = session('account_id');
        if (!$accountId) {
            abort(404);
        }
        $participant = Participant::with('event')->where('event_id', $accountId)->get();

        $data = [
            'title' => 'Participant Account',
            'link' => 'https://lms.sampharindo.id/',
            'participants' => $participant,
        ];

        return view('backend.idev.participant_account', $data);
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
