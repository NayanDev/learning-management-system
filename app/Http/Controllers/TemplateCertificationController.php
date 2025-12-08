<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\TemplateCertification;
use App\Models\User;
use Exception;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TemplateCertificationController extends DefaultController
{
    protected $modelClass = TemplateCertification::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Template Certification';
        $this->generalUri = 'template-certification';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Name', 'column' => 'name', 'order' => true],
            ['name' => 'Template', 'column' => 'view_image', 'order' => true],
            ['name' => 'Date', 'column' => 'date', 'order' => true],
            ['name' => 'Note', 'column' => 'note', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['name'],
            'headers' => [
                ['name' => 'Name', 'column' => 'name'],
                ['name' => 'Template', 'column' => 'template'],
                ['name' => 'Date', 'column' => 'date'],
            ]
        ];


        $this->importScripts = [
            ['source' => asset('vendor/select2/select2.min.js')],
            ['source' => asset('vendor/select2/select2-initialize.js')],
            ['source' => asset('vendor/signaturepad/signature_pad.min.js')],
        ];
        $this->importStyles = [
            ['source' => asset('vendor/select2/select2.min.css')],
            ['source' => asset('vendor/select2/select2-style.css')],
            ['source' => asset('vendor/signaturepad/signature.css')],
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $user = User::select(['id as value', 'name as text'])
            ->get()
            ->prepend([
                'value' => '',
                'text'  => 'Pilih peserta',
            ])
            ->toArray();

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
                'type' => 'upload',
                'label' => 'Template',
                'name' =>  'template',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('template', $id),
                'value' => (isset($edit)) ? $edit->template : ''
            ],
            [
                'type' => 'datetime',
                'label' => 'Date',
                'name' =>  'date',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('date', $id),
                'value' => (isset($edit)) ? $edit->date : ''
            ],
            [
                'type' => 'textarea',
                'label' => 'Note',
                'name' =>  'note',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('note', $id),
                'value' => (isset($edit)) ? $edit->note : ''
            ],
        ];

        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [];

        return $rules;
    }

    protected function store(Request $request)
    {
        $rules = $this->rules();

        $certificationPath = public_path('images/certification');
        if (!file_exists($certificationPath)) {
            mkdir($certificationPath, 0775, true);
        }

        $template = $request->file('template');
        if ($template) {
            $fileName = time() . '_' . $template->getClientOriginalName();
            $template->move($certificationPath, $fileName);
        } else {
            $fileName = null;
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messageErrors = (new Validation)->modify($validator, $rules);
            return response()->json([
                'status' => false,
                'alert' => 'danger',
                'message' => 'Required Form',
                'validation_errors' => $messageErrors,
            ], 200);
        }

        DB::beginTransaction();

        try {
            $insert = new TemplateCertification();
            $insert->name = $request->name;
            $insert->template = $fileName;
            $insert->date = $request->date;
            $insert->note = $request->note;
            $insert->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Was Created Successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    protected function defaultDataQuery()
    {
        $filters = [];
        $orThose = null;
        $orderBy = 'id';
        $orderState = 'DESC';
        if (request('search')) {
            $orThose = request('search');
        }
        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }

        $dataQueries = TemplateCertification::where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('template_certifications.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('template_certifications.template', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('template_certifications.date', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('template_certifications.note', 'LIKE', '%' . $orThose . '%');
            });

        $dataQueries = $dataQueries
            ->select('template_certifications.*')
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }


    public function update(Request $request, $id)
    {

        $name = $request->name;
        $template = $request->template;
        $date = $request->date;
        $note = $request->note;

        $folder = public_path('images/certification');
        if (!file_exists($folder)) {
            mkdir($folder, 0775, true);
        }

        $file = $request->file('image');

        $certificationPath = public_path('images/certification');
        if (!file_exists($certificationPath)) {
            mkdir($certificationPath, 0775, true);
        }

        $template = $request->file('template');
        if ($template) {
            $fileName = time() . '_' . $template->getClientOriginalName();
            $template->move($certificationPath, $fileName);
        } else {
            $fileName = null;
        }

        DB::beginTransaction();
        $rules = $this->rules($id);

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messageErrors = (new Validation)->modify($validator, $rules);

            return response()->json([
                'status' => false,
                'alert' => 'danger',
                'message' => 'Required Form',
                'validation_errors' => $messageErrors,
            ], 200);
        }

        try {
            $template = TemplateCertification::findOrFail($id);
            $template->name = $name;
            $template->template = $fileName;
            $template->date = $date;
            $template->note = $note;
            $template->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Was Updated Successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
