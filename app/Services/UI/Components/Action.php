<?php

namespace App\Services\UI\Components;

class Action
{
    protected array $data = [];

    public static function make(string $key): static
    {
        $instance = new static();
        $instance->data['key'] = $key;

        return $instance;
    }

    public function icon(string $icon): static
    {
        $this->data['icon'] = $icon;
        return $this;
    }

    public function class(string $class): static
    {
        $this->data['class'] = $class;
        return $this;
    }

    public function url(string $url): static
    {
        $this->data['url'] = $url;
        return $this;
    }

    public function modal(string $modal): static
    {
        $this->data['modal'] = $modal;
        return $this;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}