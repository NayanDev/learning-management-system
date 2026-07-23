<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Division;
use Exception;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DepartmentController extends DefaultController
{
    protected $modelClass = Department::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Department';
        $this->generalUri = 'department';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Division', 'column' => 'division_name', 'order' => true],
                    // ['name' => 'Uuid', 'column' => 'uuid', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['division_id'],
            'headers' => [
                    ['name' => 'Division id', 'column' => 'division_id'],
                    ['name' => 'Uuid', 'column' => 'uuid'],
                    ['name' => 'Name', 'column' => 'name'], 
            ]
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $divisionOptions = Division::select(['id as value', 'name as text'])->get();

        $fields = [
                    [
                        'type' => 'select',
                        'label' => 'Division id',
                        'name' =>  'division_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('division_id', $id),
                        'value' => (isset($edit)) ? $edit->division_id : '',
                        'options' => $divisionOptions,
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Name',
                        'name' =>  'name',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('name', $id),
                        'value' => (isset($edit)) ? $edit->name : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'name' => 'required|string',
        ];

        return $rules;
    }


    protected function defaultDataQuery()
    {
        $filters = [];
        $orThose = null;
        $orderBy = 'departments.id';
        $orderState = 'DESC';

        if (request('search')) {
            $orThose = request('search');
        }

        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }

        if (request('division_id')) {
            $filters[] = ['divisions.id', '=', request('division_id')];
        }

        $dataQueries = Department::join('divisions', 'divisions.id', 'departments.division_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->orWhere('departments.uuid', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('departments.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('divisions.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('departments.created_at', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('departments.updated_at', 'LIKE', '%' . $orThose . '%');
            })
            ->select(
                'departments.id',
                'departments.uuid',
                'departments.name',
                'divisions.name as division_name',
                'departments.created_at',
                'departments.updated_at'
            )
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }


    public function store(Request $request)
    {
        $rules = [
            'division_id' => 'required',
            'name' => 'required',
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
            // Simpan data Department
            $department = new Department();
            $department->division_id = $request->division_id;
            $department->uuid = Str::random(12);
            $department->name = $request->name;
            $department->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Department Created Successfully',
                'redirect_to' => route('department.index'),
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'alert' => 'danger',
                'message' => 'Failed to create department: ' . $e->getMessage(),
            ], 500);
        }
    }

}
