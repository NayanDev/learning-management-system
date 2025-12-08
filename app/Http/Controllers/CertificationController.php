<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Question;
use App\Models\ResultQuestion;
use App\Models\TemplateCertification;
use Idev\EasyAdmin\app\Helpers\Constant;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class CertificationController extends DefaultController
{
    protected $modelClass = Certification::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Certification';
        $this->generalUri = 'certification';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_print', 'btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Event', 'column' => 'workshop_name', 'order' => true],
            ['name' => 'Number certification', 'column' => 'number_certification', 'order' => true],
            ['name' => 'Participant', 'column' => 'participant_name', 'order' => true],
            ['name' => 'Template certification', 'column' => 'template_certification_name', 'order' => true],
            ['name' => 'Category', 'column' => 'category', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['number_certification'],
            'headers' => [
                ['name' => 'Number certification', 'column' => 'number_certification'],
                ['name' => 'Participant id', 'column' => 'participant_id'],
                ['name' => 'Category', 'column' => 'category'],
            ]
        ];


        $this->importScripts = [
            ['source' => asset('vendor/select2/select2.min.js')],
            ['source' => asset('vendor/select2/select2-initialize.js')]
        ];
        $this->importStyles = [
            ['source' => asset('vendor/select2/select2.min.css')],
            ['source' => asset('vendor/select2/select2-style.css')]
        ];
    }


    public function toRoman($number)
    {
        // Peta angka ke bilangan Romawi
        $map = [
            1000 => 'M',
            900 => 'CM',
            500 => 'D',
            400 => 'CD',
            100 => 'C',
            90 => 'XC',
            50 => 'L',
            40 => 'XL',
            10 => 'X',
            9 => 'IX',
            5 => 'V',
            4 => 'IV',
            1 => 'I'
        ];

        // Hasil konversi
        $result = '';

        // Loop untuk mengurangi angka berdasarkan nilai Romawi yang sesuai
        foreach ($map as $value => $roman) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }

        return $result;
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $currentMonth = now()->format('Y-m');
        $latestCertificate = Certification::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestCertificate) {
            $lastNumber = (int)substr($latestCertificate->number_certification, 0, 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $newCertificateNumber = sprintf("%03d", $newNumber);

        $no = $newCertificateNumber;
        $bulan = intval(date('m'));
        $romanBulan = $this->toRoman($bulan);
        $tahun = date('Y');
        $noSertifikat = $no . '/SPI/SERTIFIKAT/' . $romanBulan . '/' . $tahun;

        $participant = Participant::select(['id as value', 'name as text'])->get();
        $participantData = Participant::with(['event'])->get();
        $participantMap = $participantData->map(function ($item) {
            return [
                'value' => $item->id,
                'text' => $item->name . ' (' . $item->event->workshop->name . ')'
            ];
        })->toArray();


        $template = TemplateCertification::select(['id as value', 'name as text'])->get();

        $trainingNeeds = Event::with(['workshop'])->get();
        $training = $trainingNeeds->map(function ($item) {
            return [
                'value' => $item->id,
                'text' => ($item->workshop->name ?? '-')
            ];
        })->toArray();


        $category = [
            ['value' => 'Peserta', 'text' => 'Peserta'],
            ['value' => 'Penyelenggara', 'text' => 'Penyelenggara'],
        ];

        $fields = [
            [
                'type' => 'onlyview',
                'label' => 'Number certification',
                'name' =>  'number_certification',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('number_certification', $id),
                'value' => (isset($edit)) ? $edit->number_certification : $noSertifikat
            ],
            [
                'type' => 'select2',
                'label' => 'Participant',
                'name' =>  'participant_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('participant_id', $id),
                'value' => (isset($edit)) ? $edit->participant_id : '',
                'options' => $participantMap,
            ],
            [
                'type' => 'select2',
                'label' => 'Template Certification id',
                'name' =>  'template_certification_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('template_certification_id', $id),
                'value' => (isset($edit)) ? $edit->template_certification_id : '',
                'options' => $template,
            ],
            [
                'type' => 'select2',
                'label' => 'Event id',
                'name' =>  'event_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('event_id', $id),
                'value' => (isset($edit)) ? $edit->event_id : '',
                'options' => $training,
            ],
            [
                'type' => 'select2',
                'label' => 'Category',
                'name' =>  'category',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('category', $id),
                'value' => (isset($edit)) ? $edit->category : '',
                'options' => $category,
            ],
        ];

        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [];

        return $rules;
    }


    protected function indexApi()
    {
        $permission = (new Constant)->permissionByMenu($this->generalUri);
        $permission[] = 'print';

        $eb = [];
        $data_columns = [];
        foreach ($this->tableHeaders as $key => $col) {
            if ($key > 0) {
                $data_columns[] = $col['column'];
            }
        }

        foreach ($this->actionButtons as $key => $ab) {
            if (in_array(str_replace("btn_", "", $ab), $permission)) {
                $eb[] = $ab;
            }
        }

        $dataQueries = $this->defaultDataQuery()->paginate(10);

        $datas['extra_buttons'] = $eb;
        $datas['data_columns'] = $data_columns;
        $datas['data_queries'] = $dataQueries;
        $datas['data_permissions'] = $permission;
        $datas['uri_key'] = $this->generalUri;

        return $datas;
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

        $dataQueries = Certification::join('events', 'events.id', '=', 'certifications.event_id')
            ->join('workshops', 'workshops.id', '=', 'events.workshop_id',)
            ->join('template_certifications', 'template_certifications.id', '=', 'certifications.template_certification_id',)
            ->join('participants', 'participants.id', '=', 'certifications.participant_id',)
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('certifications.number_certification', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('template_certifications.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('certifications.category', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('participants.name', 'LIKE', '%' . $orThose . '%');
            });

        $dataQueries = $dataQueries
            ->select('certifications.*', 'workshops.name as workshop_name', 'template_certifications.name as template_certification_name', 'participants.name as participant_name')
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }

    protected function generatePDF()
    {
        $participantId = request('participant_id');
        $type = request('type');
        if (!$participantId) {
            abort(404, 'Result Question Not Found.');
        }

        $participant = Participant::where('id', $participantId)->first();
        $certification = Certification::where('participant_id', $participantId)->first();
        $template = TemplateCertification::where('id', $certification->template_certification_id)->first();

        $data = [
            'participant' => $participant,
            'certification' => $certification,
            'template' => $template,
        ];

        $pdf = PDF::loadView('pdf.certification', $data)
            ->setPaper('A4', 'landscape');

        return $pdf->stream("Certification" . ($request->year ?? date('Y')) . ".pdf");
    }
}
