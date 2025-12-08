<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\AnswerParticipant;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Question;
use App\Models\ResultQuestion;
use Exception;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isEmpty;

class QuestionController extends DefaultController
{
    protected $modelClass = Question::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Question';
        $this->generalUri = 'question';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_access', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Name', 'column' => 'question', 'order' => true],
            ['name' => 'Training', 'column' => 'workshop', 'order' => true],
            ['name' => 'Image', 'column' => 'view_image', 'order' => true],
            ['name' => 'User', 'column' => 'user', 'order' => true],
            ['name' => 'Divisi', 'column' => 'divisi', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['name'],
            'headers' => [
                ['name' => 'Name', 'column' => 'name'],
                ['name' => 'Event id', 'column' => 'event_id'],
                ['name' => 'User id', 'column' => 'user_id'],
                ['name' => 'Divisi', 'column' => 'divisi'],
            ]
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $event = request('event_id');
        if ($event) {
            $fieldTraining =
                [
                    'type' => 'hidden',
                    'label' => 'Event id',
                    'name' =>  'event_id',
                    'class' => 'col-md-12 my-2',
                    'required' => $this->flagRules('event_id', $id),
                    'value' => (isset($edit)) ? $edit->event_id : $event
                ];
        } else {
            $trainingNeeds = Event::with(['workshop'])->get();
            $training = $trainingNeeds->map(function ($item) {
                return [
                    'value' => $item->id,
                    'text' => ($item->workshop->name ?? '-')
                ];
            })->toArray();

            $fieldTraining =
                [
                    'type' => 'select',
                    'label' => 'Training',
                    'name' =>  'event_id',
                    'class' => 'col-md-12 my-2',
                    'required' => 'required',
                    'value' => (isset($edit)) ? $edit->event_id : '',
                    'options' => $training,
                ];
        }

        $fields = [
            [
                'type' => 'textarea',
                'label' => 'Question',
                'name' =>  'name',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('name', $id),
                'value' => (isset($edit)) ? $edit->name : ''
            ],
            [
                'type' => 'upload',
                'label' => 'Image (optional)',
                'name' =>  'image',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('image', $id),
                'value' => (isset($edit)) ? $edit->image : ''
            ],
            [
                'type' => 'hidden',
                'label' => 'User id',
                'name' =>  'user_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('user_id', $id),
                'value' => (isset($edit)) ? $edit->user_id : Auth::user()->id
            ],
            [
                'type' => 'hidden',
                'label' => 'Divisi',
                'name' =>  'divisi',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('divisi', $id),
                'value' => (isset($edit)) ? $edit->divisi : Auth::user()->divisi
            ],
        ];

        for ($i = 1; $i <= 4; $i++) {
            $arrAnswer = [
                'type' => 'text',
                'label' => 'Opsi ' . $i,
                'name' => 'answers[' . $i . ']',
                'class' => 'col-md-10 my-2',
                'value' => '',
                'required' => true,
            ];
            $arrPoint = [
                'type' => 'number',
                'label' => 'Point',
                'name' => 'points[' . $i . ']',
                'class' => 'col-md-2 my-2',
                'value' => 0,
                'required' => true,
            ];

            $fields[] = $arrAnswer;
            $fields[] = $arrPoint;
        }

        $fields = array_merge([$fieldTraining], $fields);

        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [];

        return $rules;
    }


    public function index()
    {
        $moreActions = [
            [
                'key' => 'import-excel-default',
                'name' => 'Import Excel',
                'html_button' => "<button id='import-excel' type='button' class='btn btn-sm btn-info radius-6' href='#' data-bs-toggle='modal' data-bs-target='#modalImportDefault' title='Import Excel' ><i class='ti ti-upload'></i></button>"
            ],
            [
                'key' => 'export-excel-default',
                'name' => 'Export Excel',
                'html_button' => "<a id='export-excel' class='btn btn-sm btn-success radius-6' target='_blank' href='" . url($this->generalUri . '-export-excel-default') . "'  title='Export Excel'><i class='ti ti-cloud-download'></i></a>"
            ],
            [
                'key' => 'export-pdf-default',
                'name' => 'Export Pdf',
                'html_button' => "<a id='export-pdf' class='btn btn-sm btn-danger radius-6' target='_blank' href='" . url($this->generalUri . '-export-pdf-default') . "' title='Export PDF'><i class='ti ti-file'></i></a>"
            ],
        ];

        $params = "";
        if (request('event_id')) {
            $params = "?event_id=" . request('event_id');
        }

        $permissions = (new Constant())->permissionByMenu($this->generalUri);
        $data['permissions'] = $permissions;
        $data['more_actions'] = $moreActions;
        $data['table_headers'] = $this->tableHeaders;
        $data['title'] = $this->title;
        $data['uri_key'] = $this->generalUri;
        $data['uri_list_api'] = route($this->generalUri . '.listapi') . $params;
        $data['uri_create'] = route($this->generalUri . '.create');
        $data['url_store'] = route($this->generalUri . '.store');
        $data['fields'] = $this->fields();
        $data['edit_fields'] = $this->fields();
        $data['actionButtonViews'] = $this->actionButtonViews;
        $data['templateImportExcel'] = "#";
        $data['filters'] = $this->filters();
        $data['drawerExtraClass'] = 'w-100';

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'easyadmin::backend.idev.list_drawer';

        return view($layout, $data);
    }


    protected function indexApi()
    {
        $permission = (new Constant)->permissionByMenu($this->generalUri);
        $permission[] = 'access';

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


    protected function store(Request $request)
    {
        $rules = $this->rules();
        $answers = $request->answers;
        $points = $request->points;

        $folder = public_path('images/soal');
        if (!file_exists($folder)) {
            mkdir($folder, 0775, true);  // Buat folder jika belum ada
        }

        $file = $request->file('image');
        if ($file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            // $file->storeAs('public/images/soal', $fileName);
            $file->move($folder, $fileName);
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
            $insert = new Question();
            $insert->name = $request->name;
            $insert->event_id = $request->event_id;
            $insert->user_id = $request->user_id;
            $insert->divisi = $request->divisi;
            $insert->image = $fileName;
            $insert->save();

            foreach ($answers as $key => $answer) {
                $insertAnswer = new Answer();
                $insertAnswer->name = $answer;
                $insertAnswer->point = $points[$key];
                $insertAnswer->question_id = $insert->id;
                $insertAnswer->user_id = $request->user_id;
                $insertAnswer->divisi = $request->divisi;
                $insertAnswer->save();
            }

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
        if (request('event_id')) {
            $filters[] = ['questions.event_id', '=', request('event_id')];
        }

        $dataQueries = Question::join('events', 'events.id', '=', 'questions.event_id')
            ->join('workshops', 'workshops.id', '=', 'events.workshop_id')
            ->join('users', 'users.id', '=', 'questions.user_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('questions.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('workshops.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('questions.divisi', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('users.name', 'LIKE', '%' . $orThose . '%');
            });

        // Cek role user
        if (Auth::user()->role->name !== 'admin') {
            $dataQueries = $dataQueries->where('questions.user_id', Auth::user()->id);
        }

        $dataQueries = $dataQueries
            ->select('questions.*', 'workshops.name as workshop', 'users.name as user')
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }


    protected function questionBarcode()
    {
        $eventId = session('question_id');
        if (!$eventId) {
            abort(404);
        }
        $event = Event::findOrFail($eventId);

        $data = [
            'title' => 'Question Access',
            'barcode' => url('assesment') . '?token=' . $event->token,
        ];

        return view('backend.idev.question_access', $data);
    }


    public function getQuestionsForTest(Request $request)
    {
        try {
            $token = request('token');
            $event = Event::where('token', $token)->first();

            $query = Question::with(['answers' => function ($q) {
                $q->orderBy('id', 'asc');
            }])
                ->where('event_id', $event->id)
                ->orderBy('id', 'asc');

            $questions = $query->orderBy('id', 'asc')->get();

            $formattedQuestions = $questions->map(function ($question) {
                if (isset($question->image)) {
                    $image = '<img src="' . asset('storage/images/soal/' . $question->image) . '" alt="Question Image" width="100%">';
                    $text = $question->name;
                    $questionData = $image . '<br>' . $text;
                } else {
                    $questionData = $question->name;
                }
                return [
                    'id' => $question->id,
                    'name' => $questionData,
                    'answers' => $question->answers->map(function ($answer) {
                        return [
                            'id' => $answer->id,
                            'name' => $answer->name,
                            'point' => $answer->point
                        ];
                    })
                ];
            });

            return response()->json([
                'status' => true,
                'questions' => $formattedQuestions
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function submitTest(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'test_employee_id' => 'required|integer',
            'event_id' => 'nullable|integer',
            'test_type' => 'required|in:pre_test,post_test',
            'user_id' => 'required|integer|exists:users,id',
            'nama_lengkap' => 'required|string',
            'email' => 'required',
            'posisi' => 'nullable|string',
            'nik' => 'nullable|string',
            'answers' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $userAnswers = $request->input('answers');
            $testType = $request->input('test_type');
            $userId = $request->input('user_id');
            $eventId = $request->input('event_id');
            $participantId = Participant::where('nik', Auth::user()->nik)->where('event_id', $eventId)->first();
            $testTypeData = ResultQuestion::where('participant_id', $participantId->id)->first();

            if ($testTypeData && ($testTypeData->posttest1_score > 0)) {
                $testType = 'post_test_2';
            }

            // Calculate total score and save detail answers to event_answers table
            $totalScore = 0;
            $answeredCount = 0;

            if ($userAnswers && is_array($userAnswers)) {
                foreach ($userAnswers as $questionIndex => $answerId) {
                    if ($answerId) {
                        $answer = Answer::find($answerId);
                        if ($answer) {
                            $totalScore += $answer->point;
                            $answeredCount++;

                            // Save detail answer to event_answers table
                            AnswerParticipant::create([
                                'user_id' => $userId,
                                'event_id' => $eventId,
                                'type' => $testType,
                                'participant_id' => $participantId->id,
                                'question_id' => $answer->question_id,
                                'answer_id' => $answer->id,
                                'point' => $answer->point
                            ]);
                        }
                    }
                }
            }

            if ($testType === 'pre_test') {
                $testType = 'pre_test';
                $pretestScore = $totalScore;
                $posttest1_score = 0;
                $posttest2_score = 0;
            } elseif ($testTypeData->posttest1_score <= 0) {
                $testType = 'post_test';
                $pretestScore = $testTypeData->pretest_score;
                $posttest1_score = $totalScore;
                $posttest2_score = 0;
            } else {
                $testType = 'post_test_2';
                $pretestScore = $testTypeData->pretest_score;
                $posttest1_score = $testTypeData->posttest1_score;
                $posttest2_score = $totalScore;
            }


            ResultQuestion::updateOrCreate(
                [
                    'participant_id' => $participantId->id,
                ],
                [
                    'user_id' => $userId,
                    'type' => $testType,
                    'pretest_score' => $pretestScore,
                    'posttest1_score' => $posttest1_score,
                    'posttest2_score' => $posttest2_score
                ]
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Test berhasil diselesaikan!',
                'data' => [
                    'score' => $totalScore,
                    'total_answers' => $answeredCount,
                    'participant_id' => $userId,
                    'test_type' => $testType,
                    'user_id' => $userId
                ]
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
                'error_detail' => $e->getTrace()
            ], 500);
        }
    }


    protected function filters()
    {
        if (Auth::user()->role->name === 'admin') {
            $isEvent = Event::get();
        } else {
            $isEvent = Event::where('user_id', Auth::user()->id)->get();
        }
        $arrEvent = [];
        $arrEvent[] = ['value' => "", 'text' => "All Event"];
        foreach ($isEvent as $key => $event) {
            $arrEvent[] = ['value' => $event->id, 'text' => $event->workshop->name];
        }
        $eventId = request('event_id');
        if (!$eventId) {
            $fields = [
                [
                    'type' => 'select2',
                    'label' => 'Event',
                    'name' => 'event_id',
                    'class' => 'col-md-2',
                    'options' => $arrEvent,
                ],
            ];
        } else {
            $fields = [];
        }

        return $fields;
    }


    public function update(Request $request, $id)
    {

        $name = $request->name;
        $eventId = $request->event_id;
        $userId = $request->user_id;
        $divisi = $request->divisi;

        $folder = public_path('images/soal');
        if (!file_exists($folder)) {
            mkdir($folder, 0775, true);  // Buat folder jika belum ada
        }

        $file = $request->file('image');

        // Periksa apakah file ada
        if ($file) {
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Hapus gambar lama jika ada
            $question = Question::findOrFail($id);
            $oldImagePath = public_path('images/soal/' . $question->image);

            // Periksa apakah file lama ada dan pastikan itu adalah file, bukan direktori
            if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                unlink($oldImagePath); // Hapus file lama
            }

            // Lanjutkan proses upload file baru (misalnya simpan file ke direktori yang baru)
            $file->move(public_path('images/soal'), $fileName);
            // Update path gambar di database atau sesuai kebutuhan
            $question->image = $fileName;
            $question->save();
        } else {
            // Tangani jika file tidak ada atau gagal di-upload
            return response()->json(['error' => 'File image tidak ditemukan.'], 400);
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
            $question = Question::findOrFail($id);

            $question->name = $name;
            $question->event_id = $eventId;
            $question->user_id = $userId;
            $question->divisi = $divisi;
            $question->image = $fileName;
            $question->save();

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
