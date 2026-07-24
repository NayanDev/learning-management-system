<?php

namespace App\Services\UI\Components;

class Tab
{
    protected array $data=[
        'active' => false,
    ];

    public static function make()
    {
        return new static();
    }

    public function icon($icon)
    {
        $this->data['icon']=$icon;
        return $this;
    }

    public function label($label)
    {
        $this->data['label']=$label;
        return $this;
    }

    public function target(string $target)
    {
        $this->data['target'] = $target;
        return $this;
    }


    public function active(bool $active = false)
    {
        $this->data['active'] = $active;
        return $this;
    }

    public function table($table)
    {
        $this->data['table']=$table;
        return $this;
    }

    public function layout($layout)
    {
        $this->data['layout']=$layout;
        return $this;
    }

    public function toArray()
    {
        return $this->data;
    }
}