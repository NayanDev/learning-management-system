<?php

namespace App\Http\Controllers;

use App\Models\DirectorSignature;
use App\Models\Event;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class DirectorSignatureController extends DefaultController
{
    protected $modelClass = DirectorSignature::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Director Signature';
        $this->generalUri = 'director-signature';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true],
                    ['name' => 'Divisi', 'column' => 'divisi', 'order' => true],
                    ['name' => 'Position', 'column' => 'position', 'order' => true],
                    ['name' => 'Signature', 'column' => 'signature', 'order' => true],
                    ['name' => 'Date', 'column' => 'date', 'order' => true],
                    ['name' => 'Approval type', 'column' => 'approval_type', 'order' => true],
                    ['name' => 'Approval id', 'column' => 'approval_id', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['name'],
            'headers' => [
                    ['name' => 'Name', 'column' => 'name'],
                    ['name' => 'Divisi', 'column' => 'divisi'],
                    ['name' => 'Position', 'column' => 'position'],
                    ['name' => 'Signature', 'column' => 'signature'],
                    ['name' => 'Date', 'column' => 'date'],
                    ['name' => 'Approval type', 'column' => 'approval_type'],
                    ['name' => 'Approval id', 'column' => 'approval_id'], 
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
                        'label' => 'Divisi',
                        'name' =>  'divisi',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('divisi', $id),
                        'value' => (isset($edit)) ? $edit->divisi : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Position',
                        'name' =>  'position',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('position', $id),
                        'value' => (isset($edit)) ? $edit->position : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Signature',
                        'name' =>  'signature',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('signature', $id),
                        'value' => (isset($edit)) ? $edit->signature : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Date',
                        'name' =>  'date',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('date', $id),
                        'value' => (isset($edit)) ? $edit->date : ''
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
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'name' => 'required|string',
                    'divisi' => 'required|string',
                    'position' => 'required|string',
                    'signature' => 'required|string',
                    'date' => 'required|string',
                    'approval_type' => 'required|string',
                    'approval_id' => 'required|string',
        ];

        return $rules;
    }

    
    public function signatureVerified($model, $id)
    {
        $approval = DirectorSignature::where('approval_type', $model)->where('approval_id', $id)->firstorfail();
        $nomordokumen = '-';

        if ($approval->approval_type === 'Event') {
            $event = Event::find($approval->approval_id);

            if ($event) {
                $nomordokumen = $event->letter_number;
            }
        }
        $map = [
            'TrainingAnalyst' => 'Analisa Kebutuhan Latihan',
            'TrainingNeed'    => 'Rencana Usulan Pelatihan',
            'Training'        => 'Training Schedule',
            'Event'           => 'Surat Perintah Pelatihan',
            'Certification'   => 'Sertifikat',
            'ReportTraining'  => 'Laporan Pelatihan',
            'Matrix'          => 'Matrix',
        ];

        $documentname = $map[$approval->approval_type];

        if ($approval) {
            return view('backend.idev.signature-verified-director', [
                'approval' => $approval, 
                'nomordokumen' => $nomordokumen,
                'documentname' => $documentname,
                ]);
        } else {
            abort(404);
        }
    }

}
