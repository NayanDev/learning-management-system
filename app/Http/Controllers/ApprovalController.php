<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Certification;
use App\Models\Event;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class ApprovalController extends DefaultController
{
    protected $modelClass = Approval::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Approval';
        $this->generalUri = 'approval';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true],
                    ['name' => 'Approval type', 'column' => 'approval_type', 'order' => true],
                    ['name' => 'Approval id', 'column' => 'approval_id', 'order' => true],
                    ['name' => 'User id', 'column' => 'user_id', 'order' => true],
                    ['name' => 'Date', 'column' => 'date', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['name'],
            'headers' => [
                    ['name' => 'Name', 'column' => 'name'],
                    ['name' => 'Approval type', 'column' => 'approval_type'],
                    ['name' => 'Approval id', 'column' => 'approval_id'],
                    ['name' => 'User id', 'column' => 'user_id'],
                    ['name' => 'Date', 'column' => 'date'], 
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
                        'label' => 'Approval type',
                        'name' =>  'approval_type',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('approval_type', $id),
                        'value' => (isset($edit)) ? $edit->approval_type : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Approval id',
                        'name' =>  'approval_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('approval_id', $id),
                        'value' => (isset($edit)) ? $edit->approval_id : ''
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
                        'label' => 'Date',
                        'name' =>  'date',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('date', $id),
                        'value' => (isset($edit)) ? $edit->date : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'name' => 'required|string',
                    'approval_type' => 'required|string',
                    'approval_id' => 'required|string',
                    'user_id' => 'required|string',
                    'date' => 'required|string',
        ];

        return $rules;
    }


    public function signatureVerified($model, $id, $user)
    {
        $approval = Approval::where('approval_type', $model)
            ->where('approval_id', $id)
            ->where('user_id', $user)
            ->firstOrFail();

        $nomordokumen = '-';

        switch ($approval->approval_type) {
            case 'Event':
                $nomordokumen = Event::findOrFail($approval->approval_id)->letter_number;
                break;

            case 'Certification':
                $nomordokumen = Certification::findOrFail($approval->approval_id)->number_certification;
                break;
        }

        return view('backend.idev.signature-verified', compact('approval', 'nomordokumen'));
    }

}
