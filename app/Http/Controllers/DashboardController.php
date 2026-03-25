<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Dashboard;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Trainer;
use Carbon\Carbon;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Support\Facades\Auth;

class DashboardController extends DefaultController
{
    protected $modelClass = Dashboard::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    protected $arrPermissions = ['list', 'show', 'create', 'edit', 'delete', 'export-excel-default', 'export-pdf-default', 'import-excel-default'];
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Dashboard';
        $this->generalUri = 'dashboard';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [
            'primaryKeys' => [''],
            'headers' => []
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $fields = [];

        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [];

        return $rules;
    }


    public function index()
    {
        $data['title'] = $this->title;
        $data['eventsAttendance'] = $this->takeTrainingAttendance();
        
        // Get current year
        $currentYear = Carbon::now()->year;
        
        // Get all events from current year
        $data['totalEvents'] = Event::whereYear('start_date', $currentYear)->count();
        
        // Events yang sudah terlaksana (end_date sudah lewat)
        $data['completedEvents'] = Event::whereYear('start_date', $currentYear)
            ->where('end_date', '<', Carbon::now())
            ->count();
        
        // Events yang belum terlaksana (end_date belum lewat)
        $data['upcomingEvents'] = Event::whereYear('start_date', $currentYear)
            ->where('end_date', '>=', Carbon::now())
            ->count();
        
        // Get TNA data from TrainingWorkshop
        $data['totalTNA'] = \App\Models\TrainingWorkshop::whereHas('trainingNeed', function($q) use ($currentYear) {
            $q->whereHas('training', function($q2) use ($currentYear) {
                $q2->where('year', $currentYear);
            });
        })->count();
        
        // TNA yang sudah terlaksana (ada event yang sudah selesai)
        $data['completedTNA'] = \App\Models\TrainingWorkshop::whereHas('trainingNeed', function($q) use ($currentYear) {
            $q->whereHas('training', function($q2) use ($currentYear) {
                $q2->where('year', $currentYear);
            });
        })->whereHas('workshop', function($q) {
            $q->whereHas('events', function($q2) {
                $q2->where('end_date', '<', Carbon::now());
            });
        })->count();
        
        // Calculate percentage
        $data['tnaPercentage'] = $data['totalTNA'] > 0 
            ? round(($data['completedTNA'] / $data['totalTNA']) * 100, 2) 
            : 0;

        $layout = 'backend.idev.participant_dashboard';

        return view($layout, $data);
    }


    public function takeTrainingAttendance()
    {
        $user = Auth::user();

        return Participant::with('event', 'attendance')
            ->where('nik', $user->nik)
            ->whereHas('event', function ($query) {
                $query->where('end_date', '>=', Carbon::today());
            })->get();
    }


    public function qrcodeScanner()
    {
        return view('backend.idev.qrcode_scanner');
    }
}
