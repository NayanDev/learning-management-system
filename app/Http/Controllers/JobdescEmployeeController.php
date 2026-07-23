<?php

namespace App\Http\Controllers;

use App\Models\JobdescEmployee;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class JobdescEmployeeController extends DefaultController
{
    protected $modelClass = JobdescEmployee::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Jobdesc Employee';
        $this->generalUri = 'jobdesc-employee';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Uuid', 'column' => 'uuid', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true],
                    ['name' => 'File', 'column' => 'file', 'order' => true],
                    ['name' => 'Employee id', 'column' => 'employee_id', 'order' => true],
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
                    ['name' => 'Employee id', 'column' => 'employee_id'],
                    ['name' => 'Is active', 'column' => 'is_active'], 
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
                        'label' => 'Employee id',
                        'name' =>  'employee_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('employee_id', $id),
                        'value' => (isset($edit)) ? $edit->employee_id : ''
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
                    'employee_id' => 'required|string',
                    'is_active' => 'required|string',
        ];

        return $rules;
    }

}
