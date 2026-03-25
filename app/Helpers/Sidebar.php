<?php

namespace App\Helpers;

use Idev\EasyAdmin\app\Helpers\Constant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class Sidebar
{

  public function generate()
  {
    $menus = $this->menus();
    $constant = new Constant();
    $permission = $constant->permissions();

    $arrMenu = [];
    foreach ($menus as $key => $menu) {
      $visibilityMenu = in_array($menu['key'] . ".index", $permission['list_access']);
      if (isset($menu['override_visibility'])) {
        $visibilityMenu = $menu['override_visibility'];
      }
      $menu['visibility'] = $visibilityMenu;
      $menu['url'] = (Route::has($menu['key'] . ".index")) ? route($menu['key'] . ".index") : "#";
      $menu['base_key'] = $menu['key'];
      $menu['key'] = $menu['key'] . ".index";

      $arrMenu[] = $menu;
    }
    return $arrMenu;
  }


  public function menus()
  {
    $role = "admin";
    if (config('idev.enable_role', true)) {
      $role = Auth::user()->role->name;
    }
    return
      [
        [
          'name' => 'Dashboard',
          'icon' => 'ti ti-dashboard',
          'key' => 'dashboard',
          'base_key' => 'dashboard',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Workshop',
          'icon' => 'ti ti-tools',
          'key' => 'workshop',
          'base_key' => 'workshop',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Training',
          'icon' => 'ti ti-device-analytics',
          'key' => 'training',
          'base_key' => 'training',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Training Analyst',
          'icon' => 'ti ti-chart-bar',
          'key' => 'training-analyst',
          'base_key' => 'training-analyst',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Training Need',
          'icon' => 'ti ti-target',
          'key' => 'training-need',
          'base_key' => 'training-need',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Training Workshop',
          'icon' => 'ti ti-briefcase',
          'key' => 'training-workshop',
          'base_key' => 'training-workshop',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Training Participant',
          'icon' => 'ti ti-user-exclamation',
          'key' => 'training-participant',
          'base_key' => 'training-participant',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Training Schedule',
          'icon' => 'ti ti-calendar-time',
          'key' => 'training-schedule',
          'base_key' => 'training-schedule',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Training Unplanned',
          'icon' => 'ti ti-calendar-plus',
          'key' => 'training-unplan',
          'base_key' => 'training-unplan',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Training Unplanned Participant',
          'icon' => 'ti ti-user-plus',
          'key' => 'training-unplan-participant',
          'base_key' => 'training-unplan-participant',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Event',
          'icon' => 'ti ti-layout-grid',
          'key' => 'event',
          'base_key' => 'event',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Trainer',
          'icon' => 'ti ti-user',
          'key' => 'trainer',
          'base_key' => 'trainer',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Participant',
          'icon' => 'ti ti-user-x',
          'key' => 'participant',
          'base_key' => 'participant',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Attendance',
          'icon' => 'ti ti-user-plus',
          'key' => 'attendance',
          'base_key' => 'attendance',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Materi',
          'icon' => 'ti ti-book',
          'key' => 'materi',
          'base_key' => 'materi',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Materi Log',
          'icon' => 'ti ti-report',
          'key' => 'materi-log',
          'base_key' => 'materi-log',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Question',
          'icon' => 'ti ti-question-mark',
          'key' => 'question',
          'base_key' => 'question',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Answer',
          'icon' => 'ti ti-award',
          'key' => 'answer',
          'base_key' => 'answer',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Result Question',
          'icon' => 'ti ti-report',
          'key' => 'result-question',
          'base_key' => 'result-question',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],

        [
          'name' => 'Answer Participant',
          'icon' => 'ti ti-award',
          'key' => 'answer-participant',
          'base_key' => 'answer-participant',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Template Certification',
          'icon' => 'ti ti-certificate',
          'key' => 'template-certification',
          'base_key' => 'template-certification',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Certification',
          'icon' => 'ti ti-certificate',
          'key' => 'certification',
          'base_key' => 'certification',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Evaluation',
          'icon' => 'ti ti-pencil',
          'key' => 'evaluation',
          'base_key' => 'evaluation',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Documentation',
          'icon' => 'ti ti-photo',
          'key' => 'documentation',
          'base_key' => 'documentation',
          'visibility' => true,
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'Role',
          'icon' => 'ti ti-key',
          'key' => 'role',
          'base_key' => 'role',
          'visibility' => in_array($role, ['admin']),
          'ajax_load' => false,
          'childrens' => []
        ],
        [
          'name' => 'User',
          'icon' => 'ti ti-users',
          'key' => 'user',
          'base_key' => 'user',
          'visibility' => in_array($role, ['admin']),
          'ajax_load' => false,
          'childrens' => []
        ],
      ];
  }


  public function defaultAllAccess($exclude = [])
  {
    return ['list', 'create', 'show', 'edit', 'delete', 'import-excel-default', 'export-excel-default', 'export-pdf-default'];
  }


  public function accessCustomize($menuKey)
  {
    $arrMenu = [
      'dashboard' => ['list'],
    ];

    return $arrMenu[$menuKey] ?? $this->defaultAllAccess();
  }
}
