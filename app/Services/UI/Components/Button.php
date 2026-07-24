<?php

namespace App\Services\UI\Components;

class Button
{
    protected array $data = [];

    public static function make(string $key)
    {
        $instance = new static();
        $instance->data['key']=$key;
        return $instance;
    }

    public function label(string $label)
    {
        $this->data['label']=$label;
        return $this;
    }

    public function class(string $class)
    {
        $this->data['class']=$class;
        return $this;
    }

    public function icon(string $icon)
    {
        $this->data['icon']=$icon;
        return $this;
    }

    public function dismiss($dismiss)
    {
        $this->data['dismiss']=$dismiss;
        return $this;
    }

    public function key()
    {
        return $this->data['key'];
    }

    public function toArray()
    {
        return $this->data;
    }

}