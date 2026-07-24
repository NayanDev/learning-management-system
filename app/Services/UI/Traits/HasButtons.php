<?php

namespace App\Services\UI\Traits;

trait HasButtons
{
    protected array $buttons = [];

    public function button($button)
    {
        $this->buttons[$button->key()] = $button->toArray();
        return $this;
    }
}