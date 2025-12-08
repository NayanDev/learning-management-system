<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use App\Models\User;
use Exception;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Idev\EasyAdmin\app\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class UserController extends DefaultController
{
    protected $modelClass = User::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'User';
        $this->generalUri = 'user';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Name', 'column' => 'name', 'order' => true],
            ['name' => 'Email', 'column' => 'email', 'order' => true],
            ['name' => 'Company', 'column' => 'company', 'order' => true],
            ['name' => 'Divisi', 'column' => 'divisi', 'order' => true],
            ['name' => 'Unit kerja', 'column' => 'unit_kerja', 'order' => true],
            ['name' => 'Status', 'column' => 'status', 'order' => true],
            ['name' => 'Gender', 'column' => 'jk', 'order' => true],
            ['name' => 'Phone', 'column' => 'telp', 'order' => true],
            ['name' => 'Nik', 'column' => 'nik', 'order' => true],
            ['name' => 'Signature', 'column' => 'view_image', 'order' => true],
            ['name' => 'Role', 'column' => 'role_name', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['name'],
            'headers' => [
                ['name' => 'Name', 'column' => 'name'],
                ['name' => 'Email', 'column' => 'email'],
                ['name' => 'Company', 'column' => 'company'],
                ['name' => 'Divisi', 'column' => 'divisi'],
                ['name' => 'Unit kerja', 'column' => 'unit_kerja'],
                ['name' => 'Status', 'column' => 'status'],
                ['name' => 'Jk', 'column' => 'jk'],
                ['name' => 'Telp', 'column' => 'telp'],
                ['name' => 'Nik', 'column' => 'nik'],
                ['name' => 'Signature', 'column' => 'signature'],
                ['name' => 'Role id', 'column' => 'role_id'],
                ['name' => 'Password', 'column' => 'password'],
            ]
        ];

        $this->importScripts = [
            ['source' => asset('vendor/select2/select2.min.js')],
            ['source' => asset('vendor/select2/select2-initialize.js')],
            ['source' => asset('vendor/signaturepad/signature_pad.min.js')],
        ];
        $this->importStyles = [
            ['source' => asset('vendor/select2/select2.min.css')],
            ['source' => asset('vendor/select2/select2-style.css')],
            ['source' => asset('vendor/signaturepad/signature.css')],
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $employeeOptions = $this->getEmployeeOptions();

        $roles = Role::get();
        $arrRole = [];
        foreach ($roles as $key => $role) {
            $arrRole[] = ['value' => $role->id, 'text' => $role->name];
        }

        $fields = [
            [
                'type' => 'user',
                'label' => 'Name',
                'name' =>  'nik',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('name', $id),
                'value' => (isset($edit)) ? $edit->nik : '',
                'options' => $employeeOptions,
                'filter' => true,
                'autofill' => true
            ],
            [
                'type' => 'text',
                'label' => 'Name',
                'name' => 'name',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->name : ''
            ],
            [
                'type' => 'text',
                'label' => 'Email',
                'name' => 'email',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->email : ''
            ],
            [
                'type' => 'text',
                'label' => 'Company',
                'name' => 'company',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->company : ''
            ],
            [
                'type' => 'text',
                'label' => 'Divisi',
                'name' => 'divisi',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->divisi : ''
            ],
            [
                'type' => 'text',
                'label' => 'Unit Kerja',
                'name' => 'unit_kerja',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->unit_kerja : ''
            ],
            [
                'type' => 'text',
                'label' => 'Status',
                'name' => 'status',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->status : ''
            ],
            [
                'type' => 'text',
                'label' => 'Gender',
                'name' => 'jk',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->jk : ''
            ],

            [
                'type' => 'text',
                'label' => 'Phone',
                'name' => 'telp',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->telp : ''
            ],
            [
                'type' => 'select2',
                'label' => 'Role',
                'name' => 'role_id',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->role_id : '',
                'options' => $arrRole
            ],
            [
                'type' => 'password',
                'label' => 'Password',
                'name' => 'password',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? '' : 'admin123',
            ],
        ];

        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [];

        return $rules;
    }


    protected function getEmployeeOptions()
    {
        try {
            $response = Http::acceptJson()->get('https://simco.sampharindogroup.com/api/pegawai');

            if ($response->successful()) {
                $employees = $response->json();
                $options = [];

                if (is_array($employees)) {
                    foreach ($employees as $employee) {
                        if (isset($employee['nik']) && isset($employee['nama'])) {
                            $options[] = [
                                'value' => $employee['nik'],
                                'text'  => $employee['nama'] . ' (' . $employee['nik'] . ')',
                                'email' => $employee['email'] ?? '',
                                'name'  => $employee['nama'] ?? '',
                                'company' => $employee['company'] ?? '',
                                'divisi' => $employee['divisi'] ?? '',
                                'unit_kerja' => $employee['unit_kerja'] ?? '',
                                'status' => $employee['status'] ?? '',
                                'jk' => $employee['jk'] ?? '',
                                'telp' => $employee['telp'] ?? '',
                            ];
                        }
                    }
                }
                return $options;
            }
        } catch (Exception $e) {
            Log::error("Gagal mengambil data pegawai untuk options: " . $e->getMessage());
        }

        return [];
    }


    public function profile()
    {
        $edit = User::where('id', Auth::user()->id)->first();

        $fields = [
            [
                'type' => 'onlyview',
                'label' => 'NIK',
                'name' => 'nik',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->nik : '',
            ],
            [
                'type' => 'text',
                'label' => 'Name',
                'name' => 'name',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->name : ''
            ],
            [
                'type' => 'text',
                'label' => 'Email',
                'name' => 'email',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->email : ''
            ],
            [
                'type' => 'onlyview',
                'label' => 'Gender',
                'name' => 'jk',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->jk : ''
            ],

            [
                'type' => 'text',
                'label' => 'Phone',
                'name' => 'telp',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->telp : ''
            ],
            [
                'type' => 'signature',
                'label' => 'Signature',
                'name' => 'signature',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit) && !empty($edit->signature)) ? asset('storage/signature/' . $edit->signature) : null,
                'required' => false,
                'accept' => 'image/png,image/jpeg,image/jpg,image/gif,image/svg+xml',
            ],
            [
                'type' => 'hidden',
                'label' => 'Role',
                'name' => 'role_id',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->role_id : '',
                // 'options' => $arrRole
            ],
            [
                'type' => 'password',
                'label' => 'Password',
                'name' => 'password',
                'class' => 'col-md-12 my-2',
                'value' => ''
            ],
            [
                'type' => 'hidden',
                'label' => 'Company',
                'name' => 'company',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->company : ''
            ],
            [
                'type' => 'hidden',
                'label' => 'Divisi',
                'name' => 'divisi',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->divisi : ''
            ],
            [
                'type' => 'hidden',
                'label' => 'Unit Kerja',
                'name' => 'unit_kerja',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->unit_kerja : ''
            ],
            [
                'type' => 'hidden',
                'label' => 'Status',
                'name' => 'status',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->status : ''
            ],
        ];

        $data['title'] = $this->title;
        $data['uri_key'] = $this->generalUri;
        $data['fields'] = $fields;
        $data['import_scripts'] = $this->importScripts;
        $data['import_styles'] = $this->importStyles;

        $layout = 'easyadmin::backend.idev.myaccount';
        if (View::exists('backend.idev.myaccount')) {
            $layout = 'backend.idev.myaccount';
        }

        return view($layout, $data);
    }


    public function store(Request $request)
    {
        $rules = $this->rules();

        // Add signature validation rule if file is uploaded
        if ($request->hasFile('signature')) {
            $rules['signature'] = 'required|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        $name = $request->name;
        $email = $request->email;
        $company = $request->company;
        $divisi = $request->divisi;
        $unit_kerja = $request->unit_kerja;
        $status = $request->status;
        $jk = $request->jk;
        $telp = $request->telp;
        $nik = $request->nik;
        $roleId = $request->role_id;
        $password = $request->password;

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
            // Handle signature file upload
            $signatureFileName = null;
            if ($request->hasFile('signature')) {
                $file = $request->file('signature');
                $signatureFileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Create directory if it doesn't exist
                $uploadPath = storage_path('app/public/signature');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Move the file to storage
                $file->move($uploadPath, $signatureFileName);
            }

            $insert = new User();
            $insert->name = $name;
            $insert->email = $email;
            $insert->nik = $nik;
            $insert->company = $company;
            $insert->divisi = $divisi;
            $insert->unit_kerja = $unit_kerja;
            $insert->status = $status;
            $insert->jk = $jk;
            $insert->telp = $telp;
            $insert->signature = $signatureFileName;
            $insert->role_id = $roleId;
            $insert->password = bcrypt($password);
            $insert->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Was Created Successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            // Delete uploaded file if there was an error
            if ($signatureFileName) {
                $filePath = storage_path('app/public/signature/' . $signatureFileName);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

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
        $orderBy = 'users.id';
        $orderState = 'DESC';
        if (request('search')) {
            $orThose = request('search');
        }
        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }
        if (request('role_id')) {
            $filters[] = ['roles.id', '=', request('role_id')];
        }

        $dataQueries = User::join('roles', 'roles.id', 'users.role_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('users.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.email', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.company', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.divisi', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.unit_kerja', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.status', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.jk', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.telp', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.nik', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('roles.name', 'LIKE', '%' . $orThose . '%');
            })
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.company',
                'users.divisi',
                'users.unit_kerja',
                'users.status',
                'users.jk',
                'users.telp',
                'users.nik',
                'users.signature',
                'users.created_at',
                'users.updated_at',
                'roles.name as role_name'
            )
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }


    public function updateProfile(Request $request)
    {
        $id = Auth::user()->id;
        $rules = $this->rules($id);

        if ($request->hasFile('signature_file')) {
            $rules['signature_file'] = 'required|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messageErrors = (new Validation)->modify($validator, $rules, 'edit_');
            return response()->json([
                'status' => false,
                'alert' => 'danger',
                'message' => 'Required Form',
                'validation_errors' => $messageErrors,
            ], 200);
        }

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $oldSignature = $user->signature;
            $signatureFileName = $oldSignature;

            // Handle signature based on method
            if ($request->input('signature_method') === 'draw' && $request->input('signature_data')) {
                $signatureData = $request->input('signature_data');

                if (strpos($signatureData, 'data:image/svg+xml') === 0) {
                    // Save SVG to storage folder
                    $svgData = str_replace('data:image/svg+xml;base64,', '', $signatureData);
                    $svgContent = base64_decode($svgData);
                    $signatureFileName = 'signature_' . time() . '_' . uniqid() . '.svg';

                    $storagePath = storage_path('app/public/signature');
                    if (!file_exists($storagePath)) {
                        mkdir($storagePath, 0755, true);
                    }

                    file_put_contents($storagePath . '/' . $signatureFileName, $svgContent);
                } else {
                    // Handle PNG fallback
                    $pngData = str_replace('data:image/png;base64,', '', $signatureData);
                    $pngContent = base64_decode($pngData);
                    $signatureFileName = 'signature_' . time() . '_' . uniqid() . '.png';

                    $storagePath = storage_path('app/public/signature');
                    if (!file_exists($storagePath)) {
                        mkdir($storagePath, 0755, true);
                    }

                    file_put_contents($storagePath . '/' . $signatureFileName, $pngContent);
                }
            } elseif ($request->hasFile('signature_file')) {
                $file = $request->file('signature_file');
                $signatureFileName = 'upload_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                $storagePath = storage_path('app/public/signature');
                if (!file_exists($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }

                $file->move($storagePath, $signatureFileName);
            }

            // Delete old signature if exists and different
            if ($oldSignature && $oldSignature !== $signatureFileName) {
                $oldPath = storage_path('app/public/signature/' . $oldSignature);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Update user data
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'nik' => $request->nik,
                'company' => $request->company,
                'divisi' => $request->divisi,
                'unit_kerja' => $request->unit_kerja,
                'status' => $request->status,
                'jk' => $request->jk,
                'telp' => $request->telp,
                'signature' => $signatureFileName,
                'role_id' => $request->role_id,
                'password' => $request->password ? bcrypt($request->password) : $user->password,
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Profile updated successfully!',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }


    protected function filters()
    {
        $kjs = Role::get();

        $arrRole = [];
        $arrRole[] = ['value' => "", 'text' => "All Roles"];
        foreach ($kjs as $key => $kj) {
            $arrRole[] = ['value' => $kj->id, 'text' => $kj->name];
        }

        $fields = [
            [
                'type' => 'select2',
                'label' => 'Role',
                'name' => 'role_id',
                'class' => 'col-md-2',
                'options' => $arrRole,
            ],
        ];

        return $fields;
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
        $data['actionButtonViews'] = [
            'easyadmin::backend.idev.buttons.delete',
            'easyadmin::backend.idev.buttons.edit',
            'easyadmin::backend.idev.buttons.show',
            'easyadmin::backend.idev.buttons.import_default',
        ];
        $data['templateImportExcel'] = "#";
        $data['filters'] = $this->filters();

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'easyadmin::backend.idev.list_drawer';

        return view($layout, $data);
    }


    public function update(Request $request, $id)
    {

        $nik = $request->nik;
        $name = $request->name;
        $email = $request->email;
        $company = $request->company;
        $divisi = $request->divisi;
        $unit_kerja = $request->unit_kerja;
        $status = $request->status;
        $jk = $request->jk;
        $telp = $request->telp;
        $roleId = $request->role_id;
        $password = $request->password;

        DB::beginTransaction();
        $rules = $this->rules($id);

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
            $user = User::findOrFail($id);

            $user->nik = $nik;
            $user->name = $name;
            $user->email = $email;
            $user->company = $company;
            $user->divisi = $divisi;
            $user->unit_kerja = $unit_kerja;
            $user->status = $status;
            $user->jk = $jk;
            $user->telp = $telp;
            $user->role_id = $roleId;
            $user->password = bcrypt($password);

            // Only update password if provided
            if (!empty($password)) {
                $user->password = bcrypt($password);
            }

            $user->save();

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


    protected function signatureBulkUpdate()
    {
        $token = request('event_id');
        if (!$token) {
            abort(403, 'Event Not Found');
        }

        $event = Event::where('id', $token)->first();
        $participants = Participant::where('event_id', $event->id)->get();
        $niks = $participants->pluck('nik');
        $users = User::whereIn('nik', $niks)->get();

        $data = [
            'users' => $users,
        ];

        // $accountId = session('account_id');
        // if (!$accountId) {
        //     abort(404);
        // }
        // $participant = User::with('event')->where('event_id', $accountId)->get();

        // $data = [
        //     'title' => 'Participant Account',
        //     'link' => 'https://lms.sampharindo.id/',
        //     'participants' => $participant,
        // ];

        return view('backend.idev.signature_bulk', $data);
    }


    public function bulkUpdateSignatures(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'users' => 'required|array|min:1',
            'users.*.id' => 'required|integer|exists:users,id',
            'users.*.signature_base64' => 'nullable|string',
        ], [
            'users.required' => 'Data user tidak boleh kosong.',
            'users.*.id.required' => 'User ID harus diisi.',
            'users.*.id.exists' => 'User tidak ditemukan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $usersData = $request->input('users');

        DB::beginTransaction();

        try {
            $updatedCount = 0;
            $errors = [];
            $storagePath = storage_path('app/public/signature');

            // Buat folder jika belum ada
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            foreach ($usersData as $userData) {
                try {
                    $userId = $userData['id'];
                    $signatureBase64 = $userData['signature_base64'] ?? null;

                    if (!$signatureBase64) {
                        continue; // Skip jika tidak ada signature
                    }

                    // Cari user
                    $user = User::findOrFail($userId);
                    $oldSignature = $user->signature;

                    // Parse signature data
                    $signatureFileName = null;

                    // ✅ Handle SVG (dari signature pad)
                    if (strpos($signatureBase64, 'data:image/svg+xml') === 0) {
                        // Remove data URI prefix
                        $svgData = preg_replace('/^data:image\/svg\+xml;base64,/', '', $signatureBase64);
                        $svgContent = base64_decode($svgData);

                        // Generate filename
                        $signatureFileName = 'signature_' . $userId . '_' . time() . '_' . uniqid() . '.svg';

                        // Save to storage
                        file_put_contents($storagePath . '/' . $signatureFileName, $svgContent);

                        Log::info('SVG signature saved', [
                            'user_id' => $userId,
                            'filename' => $signatureFileName
                        ]);
                    }
                    // ✅ Handle PNG (fallback)
                    elseif (strpos($signatureBase64, 'data:image/png') === 0) {
                        $pngData = preg_replace('/^data:image\/png;base64,/', '', $signatureBase64);
                        $pngContent = base64_decode($pngData);

                        $signatureFileName = 'signature_' . $userId . '_' . time() . '_' . uniqid() . '.png';

                        file_put_contents($storagePath . '/' . $signatureFileName, $pngContent);

                        Log::info('PNG signature saved', [
                            'user_id' => $userId,
                            'filename' => $signatureFileName
                        ]);
                    }

                    // Update user signature
                    if ($signatureFileName) {
                        $user->signature = $signatureFileName;
                        $user->save();

                        // Hapus file lama jika ada
                        if ($oldSignature && $oldSignature !== $signatureFileName) {
                            $oldPath = $storagePath . '/' . $oldSignature;
                            if (file_exists($oldPath)) {
                                unlink($oldPath);
                                Log::info('Old signature deleted', ['path' => $oldPath]);
                            }
                        }

                        $updatedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error updating user ID {$userId}: " . $e->getMessage();
                    Log::error('Bulk signature update error', [
                        'user_id' => $userId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if ($updatedCount === 0) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada signature yang berhasil disimpan. ' . implode(' ', $errors)
                ], 500);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Berhasil memperbarui {$updatedCount} tanda tangan!",
                'data' => [
                    'updated_count' => $updatedCount,
                    'total_errors' => count($errors),
                    'errors' => $errors
                ]
            ], 200);
        } catch (Exception $e) {
            DB::rollback();

            Log::error('Bulk signature update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
