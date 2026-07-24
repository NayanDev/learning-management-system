<?php

namespace App\Http\Controllers;

use App\Models\JobdescSection;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;

class JobdescSectionController extends DefaultController
{
    protected $modelClass = JobdescSection::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Jobdesc Section';
        $this->generalUri = 'jobdesc-section';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Uuid', 'column' => 'uuid', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true],
                    ['name' => 'File', 'column' => 'file', 'order' => true],
                    ['name' => 'Section id', 'column' => 'section_id', 'order' => true],
                    ['name' => 'Is active', 'column' => 'is_active', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['uuid'],
            'headers' => [
                    ['name' => 'Uuid', 'column' => 'uuid'],
                    ['name' => 'Name', 'column' => 'name'],
                    ['name' => 'File', 'column' => 'file'],
                    ['name' => 'Section id', 'column' => 'section_id'],
                    ['name' => 'Is active', 'column' => 'is_active'], 
            ]
        ];


        $this->importScripts = [
            [
                'source' => 'https://cdn.datatables.net/2.3.8/js/dataTables.min.js'
            ],
            [
                'source' => 'https://cdn.datatables.net/2.3.8/js/dataTables.bootstrap5.min.js'
            ],
            [
                'source' => asset('custom/js/initDataTable.js')
            ],
        ];

        $this->importStyles = [
            [
                'source' => 'https://cdn.datatables.net/2.3.8/css/dataTables.bootstrap5.min.css'
            ],
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
                        'label' => 'Uuid',
                        'name' =>  'uuid',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('uuid', $id),
                        'value' => (isset($edit)) ? $edit->uuid : ''
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
                        'type' => 'text',
                        'label' => 'File',
                        'name' =>  'file',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('file', $id),
                        'value' => (isset($edit)) ? $edit->file : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Section id',
                        'name' =>  'section_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('section_id', $id),
                        'value' => (isset($edit)) ? $edit->section_id : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Is active',
                        'name' =>  'is_active',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('is_active', $id),
                        'value' => (isset($edit)) ? $edit->is_active : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'uuid' => 'required|string',
                    'name' => 'required|string',
                    'file' => 'required|string',
                    'section_id' => 'required|string',
                    'is_active' => 'required|string',
        ];

        return $rules;
    }


    public function jobdescSection()
    {
        $data = $this->defaultDataQuery()
            ->where('section_id', 3)
            ->get();

        $columns = [
            [
                "title" => "NO",
                "data" => "no",
                "type" => "number",
                "width" => "5%"
            ],
            [
                "title" => "NAME",
                "data" => "name",
                "width" => "50%"
            ],
            [
                "title" => "FILE",
                "data" => "file",
                "width" => "25%"
            ],
            [
                "title" => "STATUS",
                "data" => "is_active",
                "width" => "5%"
            ],
            [
                "title" => "ACTION",
                "data" => null,
                "type" => "action",
                "width" => "15%"
            ]
        ];


        return response()->json([
            "columns" => $columns,
            "data" => $data
        ]);
    }

    public function destroyData(Request $request, $id)
    {
        $data = $this->defaultDataQuery()
            ->where('id',$id)
            ->first();

        if(!$data){
            return response()->json([
                'message'=>'Data not found'
            ],404);
        }

        $data->delete();

        return response()->json([
            'message'=>'Data deleted successfully'
        ]);
    }


    public function showData($id)
    {
        $data = $this->defaultDataQuery()
            ->where('id',$id)
            ->firstOrFail();

        return response()->json($data);
    }

}
