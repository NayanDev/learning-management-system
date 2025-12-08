<?php

namespace App\Http\Controllers;

use App\Models\MateriLog;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class MateriLogController extends DefaultController
{
    protected $modelClass = MateriLog::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Materi Log';
        $this->generalUri = 'materi-log';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Materi id', 'column' => 'materi_id', 'order' => true],
            ['name' => 'User id', 'column' => 'user_id', 'order' => true],
            ['name' => 'Action', 'column' => 'action', 'order' => true],
            ['name' => 'Count', 'column' => 'count', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['materi_id'],
            'headers' => [
                ['name' => 'Materi id', 'column' => 'materi_id'],
                ['name' => 'User id', 'column' => 'user_id'],
                ['name' => 'Action', 'column' => 'action'],
                ['name' => 'Count', 'column' => 'count'],
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
                'label' => 'Materi id',
                'name' =>  'materi_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('materi_id', $id),
                'value' => (isset($edit)) ? $edit->materi_id : ''
            ],
            [
                'type' => 'text',
                'label' => 'User id',
                'name' =>  'user_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('user_id', $id),
                'value' => (isset($edit)) ? $edit->user_id : ''
            ],
            [
                'type' => 'text',
                'label' => 'Action',
                'name' =>  'action',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('action', $id),
                'value' => (isset($edit)) ? $edit->action : ''
            ],
            [
                'type' => 'text',
                'label' => 'Count',
                'name' =>  'count',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('count', $id),
                'value' => (isset($edit)) ? $edit->count : ''
            ],
        ];

        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
            'materi_id' => 'required|string',
            'user_id' => 'required|string',
            'action' => 'required|string',
            'count' => 'required|string',
        ];

        return $rules;
    }
}
