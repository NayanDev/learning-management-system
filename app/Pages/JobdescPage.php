<?php

namespace App\Pages;

use App\Services\UI\PageBuilder;
use App\Services\UI\Components\Table;
use App\Services\UI\Components\Form;
use App\Services\UI\Components\Button;
use App\Services\UI\Components\Modal;
use App\Services\UI\Components\Tab;

class JobdescPage extends PageBuilder
{
    public function __construct()
    {
        $this->tab(Tab::make()
            ->icon('ti ti-info-circle')
            ->label('Detail')
            ->target('detail')
            ->active(true)
            ->layout('detail')
        );
        
        $this->button(Button::make('save')
            ->label('Save')
            ->class('btn btn-primary')
        );

        $this->button(Button::make('close')
            ->label('Close')
            ->class('btn btn-danger')
            ->dismiss('modal')
        );

        $this->form(Form::make('jobdesc')
            ->text('name','Name')
            ->file('file','File')
        );

        $this->modal(Modal::make('jobdesc')
            ->title('Add Jobdesc')
            ->form('jobdesc')
            ->buttons(['close','save'])
        );

        $this->table(Table::make('jobdesc')
            ->id('jobdesc-table')
            ->columns(['No','Name','Description','Action'])
            ->buttons(['edit','delete'])
            ->modal('jobdesc')
            ->url('/report/jobdesc-section')
        );

        $this->tab(Tab::make()
            ->icon('ti ti-briefcase')
            ->label('Job Description')
            ->target('jobdesc')
            ->active()
            ->layout('table')
            ->table('jobdesc')
        );
    }

}