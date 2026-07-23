<?php

namespace App\Http\Controllers;

use App\Models\MateriResume;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class MateriResumeController extends DefaultController
{
    protected $modelClass = MateriResume::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Materi Resume';
        $this->generalUri = 'materi-resume';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Uuid', 'column' => 'uuid', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true],
                    ['name' => 'File', 'column' => 'file', 'order' => true],
                    ['name' => 'Description', 'column' => 'description', 'order' => true],
                    ['name' => 'Workshop id', 'column' => 'workshop_id', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['uuid'],
            'headers' => [
                    ['name' => 'Uuid', 'column' => 'uuid'],
                    ['name' => 'Name', 'column' => 'name'],
                    ['name' => 'File', 'column' => 'file'],
                    ['name' => 'Description', 'column' => 'description'],
                    ['name' => 'Workshop id', 'column' => 'workshop_id'], 
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
                        'label' => 'Description',
                        'name' =>  'description',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('description', $id),
                        'value' => (isset($edit)) ? $edit->description : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Workshop id',
                        'name' =>  'workshop_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('workshop_id', $id),
                        'value' => (isset($edit)) ? $edit->workshop_id : ''
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
                    'description' => 'required|string',
                    'workshop_id' => 'required|string',
        ];

        return $rules;
    }

}
