<?php

namespace App\Http\Controllers;

use App\Models\TnaAdmin;
use App\Models\User;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class TnaAdminController extends DefaultController
{
    protected $modelClass = TnaAdmin::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Tna Admin';
        $this->generalUri = 'tna-admin';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Training', 'column' => 'training_id', 'order' => true],
                    ['name' => 'User', 'column' => 'user_name', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];

        $this->importScripts = [
            ['source' => asset('vendor/select2/select2.min.js')],
            ['source' => asset('vendor/select2/select2-initialize.js')],
        ];
        $this->importStyles = [
            ['source' => asset('vendor/select2/select2.min.css')],
            ['source' => asset('vendor/select2/select2-style.css')],
        ];

        $this->importExcelConfig = [ 
            'primaryKeys' => ['training_id'],
            'headers' => [
                    ['name' => 'Training id', 'column' => 'training_id'],
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

        $usersOptions = User::select('id as value', 'name as text')->get()->prepend([
            'value' => null,
            'text' => '-- Pilih User --',
        ]);

        $fields = [
                    [
                        'type' => 'onlyview',
                        'label' => 'Training id',
                        'name' =>  'training_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('training_id', $id),
                        'value' => (isset($edit)) ? $edit->training_id : request('training_id')
                    ],
                    [
                        'type' => 'select2',
                        'label' => 'User id',
                        'name' =>  'user_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('user_id', $id),
                        'value' => (isset($edit)) ? $edit->user_id : '',
                        'options' => $usersOptions
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'training_id' => 'required|string',
                    'user_id' => 'required|string',
        ];

        return $rules;
    }


    protected function defaultDataQuery()
    {
        $filters = [];
        $orThose = null;
        $orderBy = 'tna_admins.id';
        $orderState = 'DESC';

        if (request('search')) {
            $orThose = request('search');
        }

        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state', 'DESC');
        }

        if (request('training_id')) {
            $filters[] = ['trainings.id', '=', request('training_id')];
        }

        if (request('user_id')) {
            $filters[] = ['users.id', '=', request('user_id')];
        }

        $dataQueries = TnaAdmin::leftJoin(
                'trainings',
                'trainings.id',
                'tna_admins.training_id'
            )
            ->leftJoin(
                'users',
                'users.id',
                'tna_admins.user_id'
            )
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.email', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('tna_admins.created_at', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('tna_admins.updated_at', 'LIKE', '%' . $orThose . '%');
            })
            ->select(
                'tna_admins.id',
                'tna_admins.training_id',
                'tna_admins.user_id',
                'users.name as user_name',
                'users.email as user_email',
                'tna_admins.created_at',
                'tna_admins.updated_at'
            )
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }

}
