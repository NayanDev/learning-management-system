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

    public function type(string $type): static
    {
        $this->data['type'] = $type;
        return $this;
    }

    public function viewer(string $viewer): static
    {
        $this->data['viewer'] = $viewer;
        return $this;
    }

    public function src(string $src): static
    {
        $this->data['src'] = $src;
        return $this;
    }

    public function dialog(string $dialog): static
    {
        $this->data['dialog'] = $dialog;
        return $this;
    }

    public function backdrop(string $backdrop): static
    {
        $this->data['backdrop'] = $backdrop;
        return $this;
    }

    public function size(string $size): static
    {
        $this->data['size'] = $size;
        return $this;
    }

    public function keyboard(bool $keyboard): static
    {
        $this->data['keyboard'] = $keyboard;
        return $this;
    }

    public function height(int $height): static
    {
        $this->data['height'] = $height;
        return $this;
    }

    public function url(string $url): static
    {
        $this->data['url'] = $url;
        return $this;
    }

    public function method(string $method): static
    {
        $this->data['method'] = strtoupper($method);
        return $this;
    }

}