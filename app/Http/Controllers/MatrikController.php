<?php

namespace App\Http\Controllers;

use App\Models\Matrik;
use App\Models\User;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class MatrikController extends DefaultController
{
    protected $modelClass = Matrik::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Matrik';
        $this->generalUri = 'matrik';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Divisi', 'column' => 'divisi', 'order' => true],
                    ['name' => 'Created by', 'column' => 'created_by', 'order' => true],
                    ['name' => 'Checked by', 'column' => 'checked_by', 'order' => true],
                    ['name' => 'Approved by', 'column' => 'approved_by', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['divisi'],
            'headers' => [
                    ['name' => 'Divisi', 'column' => 'divisi'],
                    ['name' => 'Created by', 'column' => 'created_by'],
                    ['name' => 'Checked by', 'column' => 'checked_by'],
                    ['name' => 'Approved by', 'column' => 'approved_by'], 
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
                        'type' => 'select',
                        'label' => 'Divisi',
                        'name' =>  'divisi',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('divisi', $id),
                        'value' => (isset($edit)) ? $edit->divisi : '',
                        'options' => [
                            ['value' => "", 'text' => "Masukkan Divisi"],
                            ['value' => "UMUM & SDM", 'text' => "UMUM & SDM"],
                            ['value' => "PRODUKSI", 'text' => "PRODUKSI"],
                            ['value' => "QA", 'text' => "QA"],
                            ['value' => "SCM", 'text' => "SCM"],
                            ['value' => "RND", 'text' => "RND"],
                            ['value' => "QC", 'text' => "QC"],
                            ['value' => "TEKNIK", 'text' => "TEKNIK"],
                            ['value' => "QS", 'text' => "QS"],
                            ['value' => "SPT HOLDING", 'text' => "SPT HOLDING"],
                            ['value' => "IT", 'text' => "IT"],
                            ['value' => "KEUANGAN", 'text' => "KEUANGAN"],
                            ['value' => "CABANG SEMARANG", 'text' => "CABANG SEMARANG"],
                            ['value' => "SECURITY", 'text' => "SECURITY"]
                        ]
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Created by',
                        'name' =>  'created_by',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('created_by', $id),
                        'value' => (isset($edit)) ? $edit->created_by : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Checked by',
                        'name' =>  'checked_by',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('checked_by', $id),
                        'value' => (isset($edit)) ? $edit->checked_by : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Approved by',
                        'name' =>  'approved_by',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('approved_by', $id),
                        'value' => (isset($edit)) ? $edit->approved_by : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'divisi' => 'required|string',
        ];

        return $rules;
    }


    public function index()
    {
        $baseUrlExcel = route($this->generalUri.'.export-excel-default');
        $baseUrlPdf = route($this->generalUri.'.export-pdf-default');

        $moreActions = [
            [
                'key' => 'import-excel-default',
                'name' => 'Import Excel',
                'html_button' => "<button id='import-excel' type='button' class='btn btn-sm btn-info radius-6' href='#' data-bs-toggle='modal' data-bs-target='#modalImportDefault' title='Import Excel' ><i class='ti ti-upload'></i></button>"
            ],
            [
                'key' => 'export-excel-default',
                'name' => 'Export Excel',
                'html_button' => "<a id='export-excel' data-base-url='".$baseUrlExcel."' class='btn btn-sm btn-success radius-6' target='_blank' href='" . url($this->generalUri . '-export-excel-default') . "'  title='Export Excel'><i class='ti ti-cloud-download'></i></a>"
            ],
            [
                'key' => 'export-pdf-default',
                'name' => 'Export Pdf',
                'html_button' => "<a id='export-pdf' data-base-url='".$baseUrlPdf."' class='btn btn-sm btn-danger radius-6' target='_blank' href='" . url($this->generalUri . '-export-pdf-default') . "' title='Export PDF'><i class='ti ti-file'></i></a>"
            ],
        ];

        $permissions =  $this->arrPermissions;
        if ($this->dynamicPermission) {
            $permissions = (new Constant())->permissionByMenu($this->generalUri);
        }
        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'backend.idev.list_drawer_matrik';
        if(isset($this->drawerLayout)){
            $layout = $this->drawerLayout;
        }
        $data['permissions'] = $permissions;
        $data['more_actions'] = $moreActions;
        $data['headerLayout'] = $this->pageHeaderLayout;
        $data['table_headers'] = $this->tableHeaders;
        $data['title'] = $this->title;
        $data['uri_key'] = $this->generalUri;
        $data['uri_list_api'] = route($this->generalUri . '.listapi');
        $data['uri_create'] = route($this->generalUri . '.create');
        $data['url_store'] = route($this->generalUri . '.store');
        $data['fields'] = $this->fields();
        $data['edit_fields'] = $this->fields('edit');
        $data['actionButtonViews'] = $this->actionButtonViews;
        $data['templateImportExcel'] = "#";
        $data['import_scripts'] = $this->importScripts;
        $data['import_styles'] = $this->importStyles;
        $data['filters'] = $this->filters();
        
        return view($layout, $data);
    }


    protected function show($id)
    {
        $singleData = $this->defaultDataQuery()->where('id', $id)->first();
        $divisi = $singleData->divisi;
        $matrik = User::with([
            'attendances.participant.event.workshop'
        ])
        ->where('divisi', $divisi)
        ->get();

        $data['detail'] = $singleData;
        $data['matrik'] = $matrik;

        return view('backend.idev.show-matrik', $data);
    }

}
