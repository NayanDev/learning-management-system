<?php

namespace App\Http\Controllers;

use App\Models\JobdescEmployee;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JobdescEmployeeController extends DefaultController
{
    protected $modelClass = JobdescEmployee::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Jobdesc Employee';
        $this->generalUri = 'jobdesc-employee';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Uuid', 'column' => 'uuid', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true],
                    ['name' => 'File', 'column' => 'file', 'order' => true],
                    ['name' => 'Employee id', 'column' => 'employee_id', 'order' => true],
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
                    ['name' => 'Employee id', 'column' => 'employee_id'],
                    ['name' => 'Is active', 'column' => 'is_active'], 
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
                        'label' => 'Employee id',
                        'name' =>  'employee_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('employee_id', $id),
                        'value' => (isset($edit)) ? $edit->employee_id : ''
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
                    'employee_id' => 'required|string',
                    'is_active' => 'required|string',
        ];

        return $rules;
    }


    public function JobdescEmployee(Request $request)
    {
        $employeeId = $request->query('employee_id');
        $query = $this->defaultDataQuery();

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
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

    public function storeData(Request $request)
    {
        $request->validate(
            [
                'name'       => 'required|string|max:255',
                'file'       => 'required|file|mimes:pdf|max:1024', // 1024 KB = 1 MB
                'employee_id' => 'required',
            ],
            [
                'name.required'        => 'Nama job description wajib diisi.',
                'file.required'        => 'File PDF wajib diupload.',
                'file.file'            => 'File yang diupload tidak valid.',
                'file.mimes'           => 'File harus berformat PDF.',
                'file.max'             => 'Ukuran file terlalu besar. Maksimal upload 1 MB.',
                'employee_id.required'  => 'Employee wajib dipilih.',
            ]
        );

        $file = $request->file('file');

        // Nama file aman
        $filename = Str::slug($request->name)
            . '_' . now()->format('YmdHis')
            . '.' . $file->getClientOriginalExtension();

        // Pastikan folder tersedia
        Storage::disk('public')->makeDirectory('jobdesc/employee');

        // Simpan file ke storage/app/public/jobdesc/employee
        $file->storeAs(
            'jobdesc/employee',
            $filename,
            'public'
        );

        $record = JobdescEmployee::create([
            'uuid'       => Str::random(12),
            'name'       => $request->name,
            'file'       => $filename,
            'employee_id' => $request->employee_id,
            'is_active'  => $request->is_active ?? false,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Job description berhasil disimpan',
            'data'    => $record
        ]);
    }

    public function updateData(Request $request, $id)
    {
        $record = JobdescEmployee::findOrFail($id);

        $request->validate([
            'name'       => 'required|string|max:255',
            'file'       => 'nullable|file|mimes:pdf|max:1024',
            'employee_id' => 'required',
        ]);

        $record->name = $request->name;
        $record->employee_id = $request->employee_id;
        $record->is_active = $request->is_active ?? false;

        if ($request->hasFile('file')) {
            // hapus file lama
            if ($record->file) {
                $oldFile = 'jobdesc/employee/' . $record->file;
                if (Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                }
            }

            // pastikan folder tersedia
            Storage::disk('public')->makeDirectory('jobdesc/employee');
            $file = $request->file('file');
            // nama file sama seperti store
            $filename = Str::slug($request->name)
                . '_' . now()->format('YmdHis')
                . '.' . $file->getClientOriginalExtension();
            // simpan file
            $file->storeAs('jobdesc/employee',$filename,'public');
            $record->file = $filename;
        }

        $record->save();

        return response()->json([
            'status'  => true,
            'message' => 'Job description berhasil diperbarui.',
            'data'    => $record
        ]);
    }

    public function destroyData(Request $request, $id)
    {
        $data = JobdescEmployee::find($id);
        if (!$data) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        if ($data->file && Storage::disk('public')->exists($data->file)) {
            Storage::disk('public')->delete($data->file);
        }
        $data->delete();

        return response()->json(['message' => 'Data deleted successfully']);
    }

    public function showData($id)
    {
        $data = JobdescEmployee::findOrFail($id);

        return response()->json([
            'id'         => $data->id,
            'name'       => $data->name,
            'file'       => $data->file,
            'employee_id' => $data->employee_id,
        ]);
    }

}
