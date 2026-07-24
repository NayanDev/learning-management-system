<?php

namespace App\Services\UI\Traits;

trait HasForms
{
    protected array $forms = [];

    public function form($form)
    {
        $this->forms[$form->key()] = $form->toArray();
        return $this;
    }
}