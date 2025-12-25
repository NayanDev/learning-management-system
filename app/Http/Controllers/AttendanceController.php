<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Trainer;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends DefaultController
{
    protected $modelClass = Attendance::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Attendance';
        $this->generalUri = 'attendance';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Training', 'column' => 'workshop', 'order' => true],
            ['name' => 'Name', 'column' => 'name', 'order' => true],
            ['name' => 'Divisi', 'column' => 'divisi', 'order' => true],
            ['name' => 'User', 'column' => 'user', 'order' => true],
            ['name' => 'Date ready', 'column' => 'date_ready', 'order' => true],
            ['name' => 'Date present', 'column' => 'date_present', 'order' => true],
            ['name' => 'Date out present', 'column' => 'date_out_present', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['participant_id'],
            'headers' => [
                ['name' => 'Participant id', 'column' => 'participant_id'],
                ['name' => 'Sign ready', 'column' => 'sign_ready'],
                ['name' => 'Date ready', 'column' => 'date_ready'],
                ['name' => 'Sign present', 'column' => 'sign_present'],
                ['name' => 'Date present', 'column' => 'date_present'],
                ['name' => 'Date out present', 'column' => 'date_out_present'],
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
                'label' => 'Participant id',
                'name' =>  'participant_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('participant_id', $id),
                'value' => (isset($edit)) ? $edit->participant_id : ''
            ],
            [
                'type' => 'text',
                'label' => 'Sign ready',
                'name' =>  'sign_ready',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('sign_ready', $id),
                'value' => (isset($edit)) ? $edit->sign_ready : ''
            ],
            [
                'type' => 'text',
                'label' => 'Date ready',
                'name' =>  'date_ready',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('date_ready', $id),
                'value' => (isset($edit)) ? $edit->date_ready : ''
            ],
            [
                'type' => 'text',
                'label' => 'Sign present',
                'name' =>  'sign_present',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('sign_present', $id),
                'value' => (isset($edit)) ? $edit->sign_present : ''
            ],
            [
                'type' => 'text',
                'label' => 'Date present',
                'name' =>  'date_present',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('date_present', $id),
                'value' => (isset($edit)) ? $edit->date_present : ''
            ],
            [
                'type' => 'text',
                'label' => 'Date out present',
                'name' =>  'date_out_present',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('date_out_present', $id),
                'value' => (isset($edit)) ? $edit->date_out_present : ''
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
            $filters[] = ['participants.event_id', '=', request('event_id')];
        }

        $dataQueries = Attendance::rightJoin('participants', 'participants.id', '=', 'attendances.participant_id')
            ->join('users', 'users.id', '=', 'participants.user_id')
            ->join('events', 'events.id', '=', 'participants.event_id')
            ->join('workshops', 'workshops.id', '=', 'events.workshop_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('participants.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('participants.divisi', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('attendances.date_ready', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('attendances.date_present', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('attendances.date_out_present', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
            });

        // Cek role user
        if (Auth::user()->role->name !== 'admin') {
            $dataQueries = $dataQueries->where('participants.user_id', Auth::user()->id);
        }

        $dataQueries = $dataQueries
            ->select('attendances.*', 'users.name as user', 'participants.name as name', 'participants.divisi as divisi', 'workshops.name as workshop')
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
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

        $params = "";
        if (request('event_id')) {
            $params = "?event_id=" . request('event_id');
        }

        $permissions =  $this->arrPermissions;
        if ($this->dynamicPermission) {
            $permissions = (new Constant())->permissionByMenu($this->generalUri);
        }
        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'backend.idev.list_drawer_attendance';
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


    protected function readyPdf(Request $request)
    {
        $data = [
            'title' => 'Surat Perintah Pelatihan',
            'date' => now()->format('d M Y'),
            'participants' => Participant::with(['event', 'attendance'])->where('event_id', $request->event_id)->get(),
            'event' => Event::find($request->event_id)
        ];

        $pdf = Pdf::loadView('pdf.training_order', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->stream('surat_perintah_pelatihan.pdf');
    }


    protected function presentPdf(Request $request)
    {
        $data = [
            'title' => 'Kehadiran Peserta',
            'date' => now()->format('d M Y'),
            'participants' => Participant::with(['event', 'attendance'])->where('event_id', $request->event_id)->get(),
            'event' => Event::find($request->event_id),
            'trainer' => Trainer::where('event_id', $request->event_id)->get()
        ];

        $pdf = Pdf::loadView('pdf.training_attendance', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->stream('daftar_hadir_pelatihan.pdf');
    }


    protected function attendanceBarcode()
    {
        $eventId = session('attendance_id');
        if (!$eventId) {
            abort(404);
        }
        $event = Event::findOrFail($eventId);

        $data = [
            'title' => 'Akses Kehadiran',
            'barcode' => url('attendance-participant') . '?token=' . $event->token,
        ];

        return view('backend.idev.attendance_access', $data);
    }

    protected function checkoutBarcode()
    {
        $eventId = session('checkout_id');
        if (!$eventId) {
            abort(404);
        }
        $event = Event::findOrFail($eventId);

        $data = [
            'title' => 'Akses Checkout',
            'barcode' => url('checkout-participant') . '?token=' . $event->token,
        ];

        return view('backend.idev.attendance_checkout', $data);
    }


    public function attendanceFormReady(Request $request)
    {
        try {
            $user = Auth::user();
            $token = $request->token;

            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token tidak ditemukan.'
                ], 400);
            }

            if (!$user->nik) {
                return response()->json([
                    'status' => false,
                    'message' => 'NIK user tidak terdaftar. Silakan hubungi administrator.'
                ], 400);
            }

            $participant = Participant::with('event')
                ->where('nik', $user->nik)
                ->whereHas('event', function ($query) use ($token) {
                    $query->where('token', $token);
                })
                ->first();

            if (!$participant) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda tidak terdaftar sebagai peserta training ini atau token tidak valid.'
                ], 404);
            }

            $existingAttendance = Attendance::where('participant_id', $participant->id)
                ->whereNotNull('date_ready')
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah melakukan tanda tangan ready sebelumnya pada ' .
                        $existingAttendance->date_ready->format('d M Y H:i:s')
                ], 409);
            }

            DB::beginTransaction();

            $attendance = Attendance::updateOrCreate(
                [
                    'participant_id' => $participant->id
                ],
                [
                    'event_id' => $participant->event_id,
                    'sign_ready' => $user->id,
                    'date_ready' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Tanda tangan berhasil disimpan!',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'participant_name' => $participant->name,
                    'event_name' => $participant->event->workshop->name ?? 'N/A',
                    'date_ready' => $attendance->date_ready->format('d M Y H:i:s')
                ]
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


    public function checkoutForm(Request $request, $token)
    {
        try {
            $token = request('token');
            $user = Auth::user();

            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token tidak ditemukan.'
                ], 400);
            }

            if (!$user->nik) {
                return response()->json([
                    'status' => false,
                    'message' => 'NIK user tidak terdaftar. Silakan hubungi administrator.'
                ], 400);
            }

            $participant = Participant::with('event')
                ->where('nik', $user->nik)
                ->whereHas('event', function ($query) use ($token) {
                    $query->where('token', $token);
                })
                ->first();

            if (!$participant) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda tidak terdaftar sebagai peserta training ini atau token tidak valid.'
                ], 404);
            }

            $existingAttendance = Attendance::where('participant_id', $participant->id)
                ->whereNotNull('date_out_present')
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah melakukan tanda tangan present sebelumnya pada ' .
                        $existingAttendance->date_present->format('d M Y H:i:s')
                ], 409); // 409 Conflict
            }

            DB::beginTransaction();

            $attendance = Attendance::updateOrCreate(
                [
                    'participant_id' => $participant->id
                ],
                [
                    'event_id' => $participant->event_id,
                    'date_out_present' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Checkout berhasil disimpan!',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'participant_name' => $participant->name,
                    'event_name' => $participant->event->workshop->name ?? 'N/A',
                    'date_out_present' => $attendance->date_out_present->format('d M Y H:i:s')
                ]
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


    public function attendanceForm(Request $request, $token)
    {
        try {
            $token = request('token');
            $user = Auth::user();

            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token tidak ditemukan.'
                ], 400);
            }

            if (!$user->nik) {
                return response()->json([
                    'status' => false,
                    'message' => 'NIK user tidak terdaftar. Silakan hubungi administrator.'
                ], 400);
            }

            $participant = Participant::with('event')
                ->where('nik', $user->nik)
                ->whereHas('event', function ($query) use ($token) {
                    $query->where('token', $token);
                })
                ->first();

            if (!$participant) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda tidak terdaftar sebagai peserta training ini atau token tidak valid.'
                ], 404);
            }

            $existingAttendance = Attendance::where('participant_id', $participant->id)
                ->whereNotNull('date_present')
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah melakukan tanda tangan present sebelumnya pada ' .
                        $existingAttendance->date_present->format('d M Y H:i:s')
                ], 409); // 409 Conflict
            }

            DB::beginTransaction();

            $attendance = Attendance::updateOrCreate(
                [
                    'participant_id' => $participant->id
                ],
                [
                    'event_id' => $participant->event_id,
                    'sign_present' => $user->id,
                    'date_present' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Tanda tangan berhasil disimpan!',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'participant_name' => $participant->name,
                    'event_name' => $participant->event->workshop->name ?? 'N/A',
                    'date_present' => $attendance->date_present->format('d M Y H:i:s')
                ]
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


    public function attendance()
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

        $token = request('token');

        $user = Auth::user();

        $participant = Participant::where('nik', $user->nik)
            ->whereHas('event', function ($query) use ($token) {
                $query->where('token', $token);
            })
            ->with('event')
            ->with('attendance')
            ->first();

        if (! $participant) {
            abort(403, 'token tidak valid');
        }

        $permissions = (new Constant())->permissionByMenu($this->generalUri);
        $data['permissions'] = $permissions;
        $data['more_actions'] = $moreActions;
        $data['table_headers'] = $this->tableHeaders;
        $data['title'] = $this->title;
        $data['uri_key'] = $this->generalUri;
        $data['uri_list_api'] = route($this->generalUri . '.listapi');
        $data['uri_create'] = route($this->generalUri . '.create');
        $data['url_store'] = route($this->generalUri . '.store');
        $data['fields'] = $this->fields();
        $data['edit_fields'] = $this->fields();
        $data['actionButtonViews'] = $this->actionButtonViews;
        $data['templateImportExcel'] = "#";
        $data['filters'] = $this->filters();
        $data['drawerExtraClass'] = 'w-50';
        $data['data_query'] = $participant;

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'backend.idev.attendance_form';

        return view($layout, $data);
    }


    protected function checkout()
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

        $token = request('token');

        $user = Auth::user();

        $participant = Participant::where('nik', $user->nik)
            ->whereHas('event', function ($query) use ($token) {
                $query->where('token', $token);
            })
            ->with('event')
            ->with('attendance')
            ->first();

        if (! $participant) {
            abort(403, 'token tidak valid');
        }

        $permissions = (new Constant())->permissionByMenu($this->generalUri);
        $data['permissions'] = $permissions;
        $data['more_actions'] = $moreActions;
        $data['table_headers'] = $this->tableHeaders;
        $data['title'] = $this->title;
        $data['uri_key'] = $this->generalUri;
        $data['uri_list_api'] = route($this->generalUri . '.listapi');
        $data['uri_create'] = route($this->generalUri . '.create');
        $data['url_store'] = route($this->generalUri . '.store');
        $data['fields'] = $this->fields();
        $data['edit_fields'] = $this->fields();
        $data['actionButtonViews'] = $this->actionButtonViews;
        $data['templateImportExcel'] = "#";
        $data['filters'] = $this->filters();
        $data['drawerExtraClass'] = 'w-50';
        $data['data_query'] = $participant;

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'backend.idev.checkout_form';

        return view($layout, $data);
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
