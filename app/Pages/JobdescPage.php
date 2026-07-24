<?php

namespace App\Pages;

use App\Services\UI\Components\Action;
use App\Services\UI\Components\Button;
use App\Services\UI\Components\Form;
use App\Services\UI\Components\Modal;
use App\Services\UI\Components\Tab;
use App\Services\UI\Components\Table;
use App\Services\UI\PageBuilder;

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

        $this->button(Button::make('create')
            ->label('+ Jobdesc')
            ->class('btn btn-danger')
            ->icon('ti ti-plus')
            ->modal('jobdesc-create')
        );
        
        $this->button(Button::make('save')
            ->label('Save')
            ->class('btn btn-success')
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

        $this->modal(
            Modal::make('jobdesc-show')
                ->type('view-pdf')
                ->title('Detail Job Description')
                ->viewer('pdf')
                ->size('xl')
                ->dialog('modal-xl modal-dialog-centered')
                ->backdrop('static')
                ->keyboard(false)
                ->height(450)
                ->src(asset('storage/materi/Company Profile_1769153180.pdf') . '#toolbar=0&navpanes=0')
        );

        $this->table(Table::make('jobdesc')
            ->id('jobdesc-table')
            ->buttons([
                'create'
            ])
            ->columns(['No','Name','Description','Action'])
            ->actions([
                Action::make('show')
                    ->icon('ti ti-eye')
                    ->class('btn btn-sm btn-light-primary')
                    ->url('/section/jobdesc')
                    ->modal('jobdesc-show'),

                Action::make('edit')
                    ->icon('ti ti-edit')
                    ->class('btn btn-sm btn-light-success')
                    ->url('/section/jobdesc')
                    ->modal('jobdesc-edit'),

                Action::make('delete')
                    ->icon('ti ti-trash')
                    ->class('btn btn-sm btn-light-danger')
                    ->url('/section/jobdesc'),
            ])
            // ->modal('jobdesc-show')
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