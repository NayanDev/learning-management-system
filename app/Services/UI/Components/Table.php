<?php

namespace App\Services\UI\Components;

class Table
{
    protected array $data = [];
    
    public static function make(string $key)
    {
        $instance = new static();
        $instance->data['key'] = $key;
        return $instance;
    }

    public function id(string $id)
    {
        $this->data['id'] = $id;
        return $this;
    }

    public function url($url)
    {
        $this->data['url'] = $url;

        return $this;
    }

    public function columns(array $columns)
    {
        $this->data['columns'] = $columns;
        return $this;
    }

    public function buttons(array $buttons)
    {
        $this->data['buttons'] = $buttons;
        return $this;
    }

    public function key()
    {
        return $this->data['key'];
    }

    public function actions(array $actions): static
    {
        foreach ($actions as $action) {
            $this->data['actions'][$action->toArray()['key']] = $action->toArray();
        }

        return $this;
    }

    public function toArray()
    {
        return $this->data;
    }
}