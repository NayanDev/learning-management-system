<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Support\Facades\Auth;

class WorkshopController extends DefaultController
{
    protected $modelClass = Workshop::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $importExcelConfig;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;

    public function __construct()
    {
        $this->title = 'Workshop';
        $this->generalUri = 'workshop';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Name', 'column' => 'name', 'order' => true],
            ['name' => 'User', 'column' => 'user', 'order' => true],
            ['name' => 'Kode', 'column' => 'kode', 'order' => true],
            ['name' => 'Competency', 'column' => 'competency', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['name'],
            'headers' => [
                ['name' => 'Name', 'column' => 'name'],
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
                'type' => 'select',
                'label' => 'Kode',
                'name' =>  'kode',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('kode', $id),
                'value' => (isset($edit)) ? $edit->kode : '',
                'options' => [
                    ['value' => "", 'text' => "Masukkan Kode"],
                    ['value' => "A1", 'text' => "Line Clearence"],
                    ['value' => "A2", 'text' => "CPOB dan Validasi, Registrasi"],
                    ['value' => "A3", 'text' => "Pemahaman ISO"],
                    ['value' => "A4", 'text' => "Pelatihan Khusus (Hubungan dengan pekerjaan)"],
                    ['value' => "A5", 'text' => "K3L"],
                    ['value' => "A6", 'text' => "GDocP"],
                    ['value' => "B1", 'text' => "Leadership"],
                    ['value' => "B2", 'text' => "Pengetahuan Dasar*) Komputer, Product Knowledge, Bahasa Inggris"],
                    ['value' => "B3", 'text' => "Job Description"],
                    ['value' => "B4", 'text' => "Communication Skill"],
                    ['value' => "B5", 'text' => "VUCA"],
                    ['value' => "B6", 'text' => "AMT"],
                ]
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
                'type' => 'hidden',
                'label' => 'User id',
                'name' =>  'user_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('user_id', $id),
                'value' => (isset($edit)) ? $edit->user_id : Auth::user()->id
            ],
        ];

        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
            'name' => 'required|string',
            'user_id' => 'required|string',
        ];

        return $rules;
    }

    protected function defaultDataQuery()
    {
        $filters = [];
        $orThose = null;
        $orderBy = 'workshops.id';
        $orderState = 'DESC';
        if (request('search')) {
            $orThose = request('search');
        }
        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }

        $dataQueries = Workshop::join('users', 'users.id', '=', 'workshops.user_id')
            ->where($filters)
            ->when($orThose,function ($query) use ($orThose) {
                $query->where('workshops.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.kode', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.competency', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
            })
            ->orderBy($orderBy, $orderState)
            ->select('workshops.*', 'users.name as user', 'workshops.id as workshopId');

        return $dataQueries;
    }


    protected function show($id)
    {
        $singleData = $this->defaultDataQuery()->where('workshops.id', $id)->first();
        unset($singleData['id']);

        $data['detail'] = $singleData;

        return view('backend.idev.show-silabus', $data);
    }
}
