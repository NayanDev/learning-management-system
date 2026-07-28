<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Section;
use App\Pages\JobdescPage;
use Exception;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SectionController extends DefaultController
{
    protected $modelClass = Section::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Section';
        $this->generalUri = 'section';
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Department', 'column' => 'department_name', 'order' => true],
                    // ['name' => 'Uuid', 'column' => 'uuid', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true], 
                    ['name' => 'Has Jobdesc', 'column' => 'badge_jobdesc', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['department_id'],
            'headers' => [
                    ['name' => 'Department id', 'column' => 'department_id'],
                    ['name' => 'Uuid', 'column' => 'uuid'],
                    ['name' => 'Name', 'column' => 'name'], 
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

        $departmentOptions = Department::select(['id as value', 'name as text'])->get();

        $fields = [
                    [
                        'type' => 'select',
                        'label' => 'Department id',
                        'name' =>  'department_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('department_id', $id),
                        'value' => (isset($edit)) ? $edit->department_id : '',
                        'options' => $departmentOptions,
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
        $orderBy = 'sections.id';
        $orderState = 'DESC';

        if (request('search')) {
            $orThose = request('search');
        }

        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }

        if (request('department_id')) {
            $filters[] = ['departments.id', '=', request('department_id')];
        }

        $dataQueries = Section::join('departments', 'departments.id', 'sections.department_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->orWhere('sections.uuid', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('sections.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('departments.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('sections.created_at', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('sections.updated_at', 'LIKE', '%' . $orThose . '%');
            })
            ->select(
                'sections.id',
                'sections.uuid',
                'sections.name',
                'departments.name as department_name',
                'sections.created_at',
                'sections.updated_at'
            )
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }


    public function store(Request $request)
    {
        $rules = [
            'department_id' => 'required|exists:departments,id',
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
            // Simpan data Section
            $section = new Section();
            $section->uuid = Str::random(12);
            $section->department_id = $request->department_id;
            $section->name = $request->name;
            $section->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Section Created Successfully',
                'redirect_to' => route('section.index'),
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'alert' => 'danger',
                'message' => 'Failed to create section: ' . $e->getMessage(),
            ], 500);
        }
    }


    protected function show($id)
    {

        $singleData = $this->defaultDataQuery()
            ->where('sections.id',$id)
            ->first();

        return view('backend.idev.show_with_tab',array_merge(
                [
                    'detail'=>$singleData,
                    'title'=>$this->title,
                ],
                (new JobdescPage())->build()
            )
        );

    }


}
