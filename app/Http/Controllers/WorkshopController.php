<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class WorkshopController extends DefaultController
{
    protected $modelClass = Workshop::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Workshop';
        $this->generalUri = 'workshop';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true],
                    ['name' => 'Kode', 'column' => 'kode', 'order' => true],
                    ['name' => 'Competency', 'column' => 'competency', 'order' => true],
                    ['name' => 'User id', 'column' => 'user_id', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['name'],
            'headers' => [
                    ['name' => 'Name', 'column' => 'name'],
                    ['name' => 'Kode', 'column' => 'kode'],
                    ['name' => 'Competency', 'column' => 'competency'],
                    ['name' => 'User id', 'column' => 'user_id'], 
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
                        'label' => 'Name',
                        'name' =>  'name',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('name', $id),
                        'value' => (isset($edit)) ? $edit->name : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Kode',
                        'name' =>  'kode',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('kode', $id),
                        'value' => (isset($edit)) ? $edit->kode : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Competency',
                        'name' =>  'competency',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('competency', $id),
                        'value' => (isset($edit)) ? $edit->competency : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'User id',
                        'name' =>  'user_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('user_id', $id),
                        'value' => (isset($edit)) ? $edit->user_id : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'name' => 'required|string',
                    'kode' => 'required|string',
                    'competency' => 'required|string',
                    'user_id' => 'required|string',
        ];

        return $rules;
    }


    protected function show($id)
    {
        $singleData = $this->defaultDataQuery()->where('id', $id)->first();
        unset($singleData['id']);

        $data['detail'] = $singleData;

        return view('backend.idev.show_with_tab', $data);
    }


    public function dataTable()
    {
        $workshops = Workshop::select(
            'id',
            'name',
            'location',
            'date'
        )->get();

        return response()->json([
            'columns' => [
                [
                    'data' => 'no',
                    'title' => 'No',
                    'width' => '5%'
                ],
                [
                    'data' => 'name',
                    'title' => 'Workshop'
                ],
                [
                    'data' => 'location',
                    'title' => 'Lokasi'
                ],
                [
                    'data' => 'date',
                    'title' => 'Tanggal'
                ],
                [
                    'data' => null,
                    'title' => 'Aksi',
                    'width' => '10%'
                ]
            ],
            'data' => $workshops
        ]);
    }

}
