<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Group;
use App\Models\Position;
use App\Models\Section;
use App\Pages\EmployeePage;
use Exception;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmployeeController extends DefaultController
{
    protected $modelClass = Employee::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Employee';
        $this->generalUri = 'employee';
        $this->arrPermissions = ['show', 'edit','create','delete'];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    // ['name' => 'Uuid', 'column' => 'uuid', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true],
                    ['name' => 'Email', 'column' => 'email', 'order' => true],
                    ['name' => 'Phone', 'column' => 'phone', 'order' => true],
                    ['name' => 'Gender', 'column' => 'gender', 'order' => true],
                    ['name' => 'Has Jobdesc', 'column' => 'badge_jobdesc', 'order' => true],
                    ['name' => 'Company', 'column' => 'company_name', 'order' => true],
                    ['name' => 'Division', 'column' => 'division_name', 'order' => true],
                    ['name' => 'Department', 'column' => 'department_name', 'order' => true],
                    ['name' => 'Position', 'column' => 'position_name', 'order' => true],
                    ['name' => 'Section', 'column' => 'section_name', 'order' => true],
                    ['name' => 'Group', 'column' => 'group_name', 'order' => true],
                    ['name' => 'Status', 'column' => 'status', 'order' => true],
                    ['name' => 'Nik', 'column' => 'nik', 'order' => true],
                    ['name' => 'Signature', 'column' => 'signature', 'order' => true],
                    ['name' => 'Is trainer', 'column' => 'is_trainer', 'order' => true],
                    ['name' => 'Is leader', 'column' => 'is_leader', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['uuid'],
            'headers' => [
                    ['name' => 'Uuid', 'column' => 'uuid'],
                    ['name' => 'Name', 'column' => 'name'],
                    ['name' => 'Email', 'column' => 'email'],
                    ['name' => 'Phone', 'column' => 'phone'],
                    ['name' => 'Gender', 'column' => 'gender'],
                    ['name' => 'Company id', 'column' => 'company_id'],
                    ['name' => 'Division id', 'column' => 'division_id'],
                    ['name' => 'Department id', 'column' => 'department_id'],
                    ['name' => 'Position id', 'column' => 'position_id'],
                    ['name' => 'Section id', 'column' => 'section_id'],
                    ['name' => 'Group id', 'column' => 'group_id'],
                    ['name' => 'Status', 'column' => 'status'],
                    ['name' => 'Nik', 'column' => 'nik'],
                    ['name' => 'Signature', 'column' => 'signature'],
                    ['name' => 'Is trainer', 'column' => 'is_trainer'],
                    ['name' => 'Is leader', 'column' => 'is_leader'], 
            ]
        ];


        $this->importScripts = [
            ['source' => 'https://cdn.datatables.net/2.3.8/js/dataTables.min.js'],
            ['source' => 'https://cdn.datatables.net/2.3.8/js/dataTables.bootstrap5.min.js'],
            ['source' => asset('custom/js/initDataTable.js')],
            ['source' => asset('custom/js/modalConfig.js')],
        ];

        $this->importStyles = [
            ['source' => asset('custom/css/sweetAlertValidation.css')],
            ['source' => 'https://cdn.datatables.net/2.3.8/css/dataTables.bootstrap5.min.css'],
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $companyOptions = Company::select('id as value', 'name as text')->get()->prepend([
            'value' => null,
            'text' => '-- Pilih Company --',
        ]);

        $divisionOptions = Division::select('id as value', 'name as text')->get()->prepend([
            'value' => null,
            'text' => '-- Pilih Division --',
        ]);

        $departmentOptions = Department::select('id as value', 'name as text')->get()->prepend([
            'value' => null,
            'text' => '-- Pilih Department --',
        ]);

        $positionOptions = Position::select('id as value', 'name as text')->get()->prepend([
            'value' => null,
            'text' => '-- Pilih Position --',
        ]);

        $sectionOptions = Section::select('id as value', 'name as text')->get()->prepend([
            'value' => null,
            'text' => '-- Pilih Section --',
        ]);

        $groupOptions = Group::select('id as value', 'name as text')->get()->prepend([
            'value' => null,
            'text' => '-- Pilih Group --',
        ]);

        $fields = [
                    [
                        'type' => 'text',
                        'label' => 'Name',
                        'name' =>  'name',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('name', $id),
                        'value' => (isset($edit)) ? $edit->name : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Email',
                        'name' =>  'email',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('email', $id),
                        'value' => (isset($edit)) ? $edit->email : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Phone',
                        'name' =>  'phone',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('phone', $id),
                        'value' => (isset($edit)) ? $edit->phone : ''
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Gender',
                        'name' =>  'gender',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('gender', $id),
                        'value' => (isset($edit)) ? $edit->gender : '',
                        'options' => [
                            ['value' => 'male', 'text' => 'male'],
                            ['value' => 'female', 'text' => 'female']
                        ]
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Company',
                        'name' => 'company_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('company_id', $id),
                        'value' => isset($edit) ? $edit->company_id : '',
                        'options' => $companyOptions,
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Division',
                        'name' => 'division_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('division_id', $id),
                        'value' => isset($edit) ? $edit->division_id : '',
                        'options' => $divisionOptions,
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Department',
                        'name' => 'department_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('department_id', $id),
                        'value' => isset($edit) ? $edit->department_id : '',
                        'options' => $departmentOptions,
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Position',
                        'name' => 'position_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('position_id', $id),
                        'value' => isset($edit) ? $edit->position_id : '',
                        'options' => $positionOptions,
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Section',
                        'name' => 'section_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('section_id', $id),
                        'value' => isset($edit) ? $edit->section_id : '',
                        'options' => $sectionOptions,
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Group',
                        'name' => 'group_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('group_id', $id),
                        'value' => isset($edit) ? $edit->group_id : '',
                        'options' => $groupOptions,
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Status',
                        'name' =>  'status',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('status', $id),
                        'value' => (isset($edit)) ? $edit->status : '',
                        'options' => [
                            ['value' => '', 'text' => 'Pilih Status'],
                            ['value' => 'Staff', 'text' => 'Staff'],
                            ['value' => 'Karyawan Baru', 'text' => 'Karyawan Baru'],
                            ['value' => 'Bulanan Tetap', 'text' => 'Bulanan Tetap'],
                            ['value' => 'Bulanan Kontrak', 'text' => 'Bulanan Kontrak'],
                            ['value' => 'Harian Kontrak', 'text' => 'Harian Kontrak'],
                        ]
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Nik',
                        'name' =>  'nik',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('nik', $id),
                        'value' => (isset($edit)) ? $edit->nik : ''
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Is trainer',
                        'name' =>  'is_trainer',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('is_trainer', $id),
                        'value' => (isset($edit)) ? $edit->is_trainer : '',
                        'options' => [
                            ['value' => 0, 'text' => 'No'],
                            ['value' => 1, 'text' => 'Yes'],
                        ]
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Is leader',
                        'name' =>  'is_leader',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('is_leader', $id),
                        'value' => (isset($edit)) ? $edit->is_leader : '',
                        'options' => [
                            ['value' => 0, 'text' => 'No'],
                            ['value' => 1, 'text' => 'Yes'],
                        ]
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'uuid' => 'required|string',
                    'name' => 'required|string',
                    'email' => 'required|string',
                    'phone' => 'required|string',
                    'gender' => 'required|string',
                    'company_id' => 'required|string',
                    'status' => 'required|string',
                    'nik' => 'required|string',
                    'is_trainer' => 'required|string',
                    'is_leader' => 'required|string',
        ];

        return $rules;
    }


    protected function defaultDataQuery()
    {
        $filters = [];
        $orThose = null;
        $orderBy = 'employees.id';
        $orderState = 'DESC';

        if (request('search')) {
            $orThose = request('search');
        }

        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }

        if (request('company_id')) {
            $filters[] = ['companies.id', '=', request('company_id')];
        }

        if (request('division_id')) {
            $filters[] = ['divisions.id', '=', request('division_id')];
        }

        if (request('department_id')) {
            $filters[] = ['departments.id', '=', request('department_id')];
        }

        if (request('position_id')) {
            $filters[] = ['positions.id', '=', request('position_id')];
        }

        if (request('section_id')) {
            $filters[] = ['sections.id', '=', request('section_id')];
        }

        if (request('group_id')) {
            $filters[] = ['groups.id', '=', request('group_id')];
        }

        $dataQueries = Employee::leftJoin('companies', 'companies.id', 'employees.company_id')
            ->leftJoin('divisions', 'divisions.id', 'employees.division_id')
            ->leftJoin('departments', 'departments.id', 'employees.department_id')
            ->leftJoin('positions', 'positions.id', 'employees.position_id')
            ->leftJoin('sections', 'sections.id', 'employees.section_id')
            ->leftJoin('groups', 'groups.id', 'employees.group_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->orWhere('employees.uuid', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('employees.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('employees.email', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('employees.phone', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('employees.gender', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('employees.nik', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('employees.status', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('companies.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('divisions.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('departments.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('positions.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('sections.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('groups.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('employees.created_at', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('employees.updated_at', 'LIKE', '%' . $orThose . '%');
            })
            ->select(
                'employees.id',
                'employees.uuid',
                'employees.name',
                'employees.email',
                'employees.phone',
                'employees.gender',
                'employees.nik',
                'employees.status',
                'employees.signature',
                'employees.is_trainer',
                'employees.is_leader',
                'companies.name as company_name',
                'divisions.name as division_name',
                'departments.name as department_name',
                'positions.name as position_name',
                'sections.name as section_name',
                'groups.name as group_name',
                'employees.created_at',
                'employees.updated_at'
            )
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }


    public function store(Request $request)
    {
        $rules = [
            'name'          => 'required',
            'email'         => 'required|email|unique:employees,email',
            'phone'         => 'required',
            'gender'        => 'required',
            'company_id'    => 'required|exists:companies,id',
            'status'        => 'required',
            'nik'           => 'required|unique:employees,nik',
            'is_trainer'    => 'required|boolean',
            'is_leader'     => 'required|boolean',
        ];

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
            // Simpan data Employee
            $employee = new Employee();
            $employee->uuid = Str::random(12);
            $employee->name = $request->name;
            $employee->email = $request->email;
            $employee->phone = $request->phone;
            $employee->gender = $request->gender;
            $employee->company_id = $request->company_id;
            $employee->division_id = $request->division_id;
            $employee->department_id = $request->department_id;
            $employee->position_id = $request->position_id;
            $employee->section_id = $request->section_id;
            $employee->group_id = $request->group_id;
            $employee->status = $request->status;
            $employee->nik = $request->nik;
            $employee->signature = null;
            $employee->is_trainer = $request->is_trainer;
            $employee->is_leader = $request->is_leader;
            $employee->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Employee Created Successfully',
                'redirect_to' => route('employee.index'),
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'alert' => 'danger',
                'message' => 'Failed to create employee: ' . $e->getMessage(),
            ], 500);
        }
    }


    protected function show($id)
    {

        $singleData = $this->defaultDataQuery()
            ->where('employees.id',$id)
            ->first();

        return view('backend.idev.show_with_tab',array_merge(
                [
                    'detail'=>$singleData,
                    'title'=>$this->title,
                ],
                (new EmployeePage())->build()
            )
        );

    }

}
