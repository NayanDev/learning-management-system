<?php

namespace App\Services\UI\Traits;

trait HasTabs
{
    protected array $tabs = [];

    public function tab($tab)
    {
        $this->tabs[] = $tab->toArray();
        return $this;
    }
}