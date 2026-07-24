<?php

namespace App\Services\UI\Traits;

trait HasModals
{
    protected array $modals = [];

    public function modal($modal)
    {
        $this->modals[$modal->key()] = $modal->toArray();
        return $this;
    }
}