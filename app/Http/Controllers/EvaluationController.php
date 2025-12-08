<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Event;
use App\Models\Participant;
use Exception;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluationController extends DefaultController
{
    protected $modelClass = Evaluation::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $importExcelConfig;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;

    public function __construct()
    {
        $this->title = 'Evaluation';
        $this->generalUri = 'evaluation';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Name', 'column' => 'name', 'order' => true],
            ['name' => 'Type', 'column' => 'type', 'order' => true],
            ['name' => 'Event id', 'column' => 'event_id', 'order' => true],
            ['name' => 'Participant id', 'column' => 'participant_id', 'order' => true],
            ['name' => 'Score', 'column' => 'score', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['name'],
            'headers' => [
                ['name' => 'Name', 'column' => 'name'],
                ['name' => 'Type', 'column' => 'type'],
                ['name' => 'Event id', 'column' => 'event_id'],
                ['name' => 'Participant id', 'column' => 'participant_id'],
                ['name' => 'Score', 'column' => 'score'],
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
                'label' => 'Type',
                'name' =>  'type',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('type', $id),
                'value' => (isset($edit)) ? $edit->type : ''
            ],
            [
                'type' => 'text',
                'label' => 'Event id',
                'name' =>  'event_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('event_id', $id),
                'value' => (isset($edit)) ? $edit->event_id : ''
            ],
            [
                'type' => 'text',
                'label' => 'Participant id',
                'name' =>  'participant_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('participant_id', $id),
                'value' => (isset($edit)) ? $edit->participant_id : ''
            ],
            [
                'type' => 'text',
                'label' => 'Score',
                'name' =>  'score',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('score', $id),
                'value' => (isset($edit)) ? $edit->score : ''
            ],
        ];

        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
            'name' => 'required|string',
            'type' => 'required|string',
            'event_id' => 'required|string',
            'participant_id' => 'required|string',
            'score' => 'required|string',
        ];

        return $rules;
    }


    protected function evaluationForm()
    {
        $token = request('token');
        if (!$token) {
            abort(403, 'Token Not Found.');
        }

        $eventId = Event::where('token', $token)->first();
        $participantId = Participant::where('nik', Auth::user()->nik)->where('event_id', $eventId->id)->first();
        dd($participantId->id);
        $evaluation = Evaluation::where('event_id', $eventId->id)->where('participant_id', $participantId->id)->first();

        if ($evaluation) {
            abort(403, 'Anda sudah mengisi form evaluasi untuk event ini.');
        }

        if (!$participantId) {
            abort(403, 'Anda tidak terdaftar pada event ini.');
        }

        return view('backend.idev.evaluation_form');
    }


    protected function evaluationBulk()
    {
        $token = request('token');
        if (!$token) {
            abort(403, 'Token Not Found.');
        }
        $event = Event::where('token', $token)->first();
        $participants = Participant::where('event_id', $event->id)->get();

        $data = [
            'event' => $event,
            'participants' => $participants,
        ];

        return view('backend.idev.evaluation_bulk', $data);
    }


    protected function submitEvaluation(Request $request)
    {
        $token = $request->token;
        if (!$token) {
            abort(403, 'Token Not Found.');
        }
        $eventId = Event::where('token', $token)->first();
        $participantId = Participant::where('nik', Auth::user()->nik)->where('event_id', $eventId->id)->first();
        if (!$participantId) {
            abort(403, 'Anda tidak terdaftar pada event ini.');
        }

        DB::beginTransaction();

        try {
            $data = $request->input('evaluations');
            foreach ($data as $namaAspek => $nilai) {
                Evaluation::create([
                    'name'      => $namaAspek,
                    'type'      => 'peserta',
                    'event_id'  => $eventId->id,
                    'participant_id' => $participantId->id,
                    'score' => $nilai ?: null,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Evaluation submitted successfully.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
                'error_detail' => $e->getTrace()
            ], 500);
        }
    }


    protected function submitEvaluationBulk(Request $request)
    {
        $evaluation = $request->input('evaluations');
        $data = [
            'evaluation' => $evaluation
        ];
        dd($data);
        $token = $request->token;
        if (!$token) {
            abort(403, 'Token Not Found.');
        }
        $eventId = Event::where('token', $token)->first();
        $participantId = Participant::where('nik', Auth::user()->nik)->where('event_id', $eventId->id)->first();
        if (!$participantId) {
            abort(403, 'Anda tidak terdaftar pada event ini.');
        }

        DB::beginTransaction();

        try {
            foreach ($evaluation as $eval) {
                // Melakukan penyimpanan untuk setiap aspek dari peserta
                foreach (['penguasaan_teori', 'penguasaan_praktek', 'kedisiplinan_dan_prilaku'] as $aspek) {
                    Evaluation::create([
                        'name'           => $aspek,
                        'type'           => 'penyelenggara',
                        'event_id'       => $eventId->id,
                        'participant_id' => $participantId->id,
                        'score'          => $eval[$aspek] ?: null, // Menyimpan nilai aspek atau null
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Evaluation submitted successfully.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
                'error_detail' => $e->getTrace()
            ], 500);
        }
    }


    protected function generatePDF(Request $request)
    {
        // $participantId = request('participant_id');
        // $type = request('type');
        // if (!$participantId) {
        //     abort(404, 'Result Question Not Found.');
        // }

        // $participant = Participant::where('id', $participantId)->first();
        // $certification = Certification::where('participant_id', $participantId)->first();

        // $data = [
        //     'participant' => $participant,
        //     'certification' => $certification,
        // ];

        $pdf = PDF::loadView('pdf.evaluation')
            ->setPaper('A4', 'portrait');

        return $pdf->stream("Evaluation" . ($request->year ?? date('Y')) . ".pdf");
    }
}
