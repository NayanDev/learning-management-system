<?php

namespace App\Http\Controllers;

use App\Models\JobdescSection;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JobdescSectionController extends DefaultController
{
    protected $modelClass = JobdescSection::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title      = 'Jobdesc Section';
        $this->generalUri = 'jobdesc-section';
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Uuid', 'column' => 'uuid', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true],
                    ['name' => 'File', 'column' => 'file', 'order' => true],
            ['name' => 'Section id', 'column' => 'section_id', 'order' => true],
                    ['name' => 'Is active', 'column' => 'is_active', 'order' => true], 
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => ['uuid'],
            'headers' => [
                    ['name' => 'Uuid', 'column' => 'uuid'],
                    ['name' => 'Name', 'column' => 'name'],
                    ['name' => 'File', 'column' => 'file'],
                ['name' => 'Section id', 'column' => 'section_id'],
                    ['name' => 'Is active', 'column' => 'is_active'], 
            ]
        ];

        $this->importScripts = [
            ['source' => 'https://cdn.datatables.net/2.3.8/js/dataTables.min.js'],
            ['source' => 'https://cdn.datatables.net/2.3.8/js/dataTables.bootstrap5.min.js'],
            ['source' => asset('custom/js/initDataTable.js')],
        ];

        $this->importStyles = [
            // ['source' => asset('custom/css/sweetAlertValidation.css')],
            ['source' => 'https://cdn.datatables.net/2.3.8/css/dataTables.bootstrap5.min.css'],
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
                        'label' => 'Uuid',
                        'name' =>  'uuid',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('uuid', $id),
                        'value' => (isset($edit)) ? $edit->uuid : ''
                    ],
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
                        'label' => 'File',
                        'name' =>  'file',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('file', $id),
                        'value' => (isset($edit)) ? $edit->file : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Section id',
                        'name' =>  'section_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('section_id', $id),
                        'value' => (isset($edit)) ? $edit->section_id : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Is active',
                        'name' =>  'is_active',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('is_active', $id),
                        'value' => (isset($edit)) ? $edit->is_active : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'uuid' => 'required|string',
                    'name' => 'required|string',
                    'file' => 'required|string',
            'section_id' => 'required|string',
                    'is_active' => 'required|string',
        ];

        return $rules;
    }


    public function jobdescSection(Request $request)
    {
        $sectionId = $request->query('section_id');

        $query = $this->defaultDataQuery();

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $data = $query->get();

        $columns = [
            ['title' => 'NO',     'data' => 'no',       'type' => 'number', 'width' => '5%'],
            ['title' => 'NAME',   'data' => 'name',                          'width' => '45%'],
            ['title' => 'FILE',   'data' => 'file',                          'width' => '20%'],
            ['title' => 'STATUS', 'data' => 'is_active', 'type' => 'status', 'width' => '5%'],
            ['title' => 'ACTION', 'data' => null,         'type' => 'action', 'width' => '15%'],
        ];

        return response()->json([
            'columns' => $columns,
            'data'    => $data,
        ]);
    }



    /**
     * POST /section/jobdesc
     * Create a new JobdescSection with PDF file upload.
     */

    public function storeData(Request $request)
    {
        $request->validate(
            [
                'name'       => 'required|string|max:255',
                'file'       => 'required|file|mimes:pdf|max:1024', // 1024 KB = 1 MB
                'section_id' => 'required',
            ],
            [
                'name.required'        => 'Nama job description wajib diisi.',
                'file.required'        => 'File PDF wajib diupload.',
                'file.file'            => 'File yang diupload tidak valid.',
                'file.mimes'           => 'File harus berformat PDF.',
                'file.max'             => 'Ukuran file terlalu besar. Maksimal upload 1 MB.',
                'section_id.required'  => 'Section wajib dipilih.',
            ]
        );

        $file = $request->file('file');

        // Nama file aman
        $filename = Str::slug($request->name)
            . '_' . now()->format('YmdHis')
            . '.' . $file->getClientOriginalExtension();

        // Pastikan folder tersedia
        Storage::disk('public')->makeDirectory('jobdesc/section');

        // Simpan file ke storage/app/public/jobdesc/section
        $file->storeAs(
            'jobdesc/section',
            $filename,
            'public'
        );

        $record = JobdescSection::create([
            'uuid'       => Str::random(12),
            'name'       => $request->name,
            'file'       => $filename, // hanya nama file
            'section_id' => $request->section_id,
            'is_active'  => $request->is_active ?? false,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Job description berhasil disimpan',
            'data'    => $record
        ]);
    }

    /**
     * PUT /section/jobdesc/{id}
     * Update a JobdescSection. File upload is optional.
     */
    public function updateData(Request $request, $id)
    {
        $record = JobdescSection::findOrFail($id);

        $request->validate([
            'name'       => 'required|string|max:255',
            'file'       => 'nullable|file|mimes:pdf|max:1024',
            'section_id' => 'required',
        ]);


        $record->name = $request->name;
        $record->section_id = $request->section_id;
        $record->is_active = $request->is_active ?? false;


        if ($request->hasFile('file')) {

            // hapus file lama
            if ($record->file) {

                $oldFile = 'jobdesc/section/' . $record->file;

                if (Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                }
            }


            // pastikan folder tersedia
            Storage::disk('public')
                ->makeDirectory('jobdesc/section');


            $file = $request->file('file');


            // nama file sama seperti store
            $filename = Str::slug($request->name)
                . '_' . now()->format('YmdHis')
                . '.' . $file->getClientOriginalExtension();


            // simpan file
            $file->storeAs(
                'jobdesc/section',
                $filename,
                'public'
            );


            // database hanya nama file
            $record->file = $filename;
        }


        $record->save();


        return response()->json([
            'status'  => true,
            'message' => 'Job description berhasil diperbarui.',
            'data'    => $record
        ]);
    }


    /**
     * DELETE /section/jobdesc/{id}
     */
    public function destroyData(Request $request, $id)
    {
        $data = JobdescSection::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        if ($data->file && Storage::disk('public')->exists($data->file)) {
            Storage::disk('public')->delete($data->file);
        }

        $data->delete();

        return response()->json(['message' => 'Data deleted successfully']);
    }


    /**
     * GET /section/jobdesc/{id}
     * Returns fields for pre-filling the edit modal.
     */
    public function showData($id)
    {
        $data = JobdescSection::findOrFail($id);

        return response()->json([
            'id'         => $data->id,
            'name'       => $data->name,
            'file'       => $data->file,
            'section_id' => $data->section_id,
        ]);
    }
}
