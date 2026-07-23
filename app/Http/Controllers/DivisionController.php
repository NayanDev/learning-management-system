<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Division;
use Exception;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DivisionController extends DefaultController
{
    protected $modelClass = Division::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Division';
        $this->generalUri = 'division';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Company', 'column' => 'company_name', 'order' => true],
                    // ['name' => 'Uuid', 'column' => 'uuid', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true],
                    ['name' => 'Is functional', 'column' => 'is_functional', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['company_id'],
            'headers' => [
                    ['name' => 'Company id', 'column' => 'company_id'],
                    ['name' => 'Uuid', 'column' => 'uuid'],
                    ['name' => 'Name', 'column' => 'name'],
                    ['name' => 'Is functional', 'column' => 'is_functional'], 
            ]
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }
        
        $companyOptions = Company::select(['id as value', 'name as text'])->get();

        $fields = [
                    [
                        'type' => 'select',
                        'label' => 'Company id',
                        'name' =>  'company_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('company_id', $id),
                        'value' => (isset($edit)) ? $edit->company_id : '',
                        'options' => $companyOptions
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Name',
                        'name' =>  'name',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('name', $id),
                        'value' => (isset($edit)) ? $edit->name : ''
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Is functional',
                        'name' =>  'is_functional',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('is_functional', $id),
                        'value' => (isset($edit)) ? $edit->is_functional : '',
                        'options' => [
                            ['value' => 1, 'text' => 'Yes'],
                            ['value' => 0, 'text' => 'No'],
                        ]
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
        $orderBy = 'divisions.id';
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

        $dataQueries = Division::join('companies', 'companies.id', 'divisions.company_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->orWhere('divisions.uuid', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('divisions.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('companies.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('divisions.is_functional', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('divisions.created_at', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('divisions.updated_at', 'LIKE', '%' . $orThose . '%');
            })
            ->select(
                'divisions.id',
                'divisions.uuid',
                'divisions.name',
                'divisions.is_functional',
                'companies.name as company_name',
                'divisions.created_at',
                'divisions.updated_at'
            )
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }


    public function store(Request $request)
    {
        $rules = [
            'company_id' => 'required',
            'name' => 'required',
            'is_functional' => 'required',
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
            // Simpan data Division
            $division = new Division();
            $division->company_id = $request->company_id;
            $division->uuid = Str::random(12);
            $division->name = $request->name;
            $division->is_functional = $request->is_functional;
            $division->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Division Created Successfully',
                'redirect_to' => route('division.index'),
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'alert' => 'danger',
                'message' => 'Failed to create division: ' . $e->getMessage(),
            ], 500);
        }
    }

}
