<?php

namespace App\Services\UI\Components;

class Modal
{
    protected array $data=[];

    public static function make(string $key)
    {
        $instance = new static();

        $instance->data['key'] = $key;
        $instance->data['id'] = $key;

        return $instance;
    }


    public function title($title)
    {
        $this->data['title']=$title;
        return $this;
    }


    public function form($form)
    {
        $this->data['form']=$form;
        return $this;
    }


    public function buttons(array $buttons)
    {
        $this->data['buttons']=$buttons;
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