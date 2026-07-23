<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Section;
use Exception;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GroupController extends DefaultController
{
    protected $modelClass = Group::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Group';
        $this->generalUri = 'group';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Section', 'column' => 'section_name', 'order' => true],
                    // ['name' => 'Uuid', 'column' => 'uuid', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['section_id'],
            'headers' => [
                    ['name' => 'Section id', 'column' => 'section_id'],
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

        $sectionOptions = Section::select(['id as value', 'name as text'])->get();

        $fields = [
                    [
                        'type' => 'select',
                        'label' => 'Section id',
                        'name' =>  'section_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('section_id', $id),
                        'value' => (isset($edit)) ? $edit->section_id : '',
                        'options' => $sectionOptions,
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
                    'section_id' => 'required|string',
                    'uuid' => 'required|string',
                    'name' => 'required|string',
        ];

        return $rules;
    }


    protected function defaultDataQuery()
    {
        $filters = [];
        $orThose = null;
        $orderBy = 'groups.id';
        $orderState = 'DESC';

        if (request('search')) {
            $orThose = request('search');
        }

        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }

        if (request('section_id')) {
            $filters[] = ['sections.id', '=', request('section_id')];
        }

        $dataQueries = Group::join('sections', 'sections.id', 'groups.section_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->orWhere('groups.uuid', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('groups.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('sections.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('groups.created_at', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('groups.updated_at', 'LIKE', '%' . $orThose . '%');
            })
            ->select(
                'groups.id',
                'groups.uuid',
                'groups.name',
                'sections.name as section_name',
                'groups.created_at',
                'groups.updated_at'
            )
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }


    public function store(Request $request)
    {
        $rules = [
            'section_id' => 'required|exists:sections,id',
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
            // Simpan data Group
            $group = new Group();
            $group->uuid = Str::random(12);
            $group->section_id = $request->section_id;
            $group->name = $request->name;
            $group->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Group Created Successfully',
                'redirect_to' => route('group.index'),
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'alert' => 'danger',
                'message' => 'Failed to create group: ' . $e->getMessage(),
            ], 500);
        }
    }

}
