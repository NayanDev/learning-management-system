<?php

namespace App\Services\UI;

use App\Services\UI\Traits\HasTabs;
use App\Services\UI\Traits\HasTables;
use App\Services\UI\Traits\HasForms;
use App\Services\UI\Traits\HasButtons;
use App\Services\UI\Traits\HasModals;

class PageBuilder
{
    use HasTabs;
    use HasTables;
    use HasForms;
    use HasButtons;
    use HasModals;

    public function build(): array
    {
        return [
            'tabs' => $this->tabs,
            'components' => [
                'tables' => $this->tables,
                'forms' => $this->forms,
                'buttons' => $this->buttons,
                'modals' => $this->modals,
            ],
        ];
    }
}