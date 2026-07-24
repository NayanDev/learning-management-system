<?php

namespace App\Services\UI\Components;

class Form
{

    protected array $data=[];

    public static function make(string $key)
    {
        $instance=new static();
        $instance->data['key']=$key;
        $instance->data['fields']=[];
        return $instance;
    }


    public function field(array $field)
    {
        $this->data['fields'][]=$field;
        return $this;
    }

    public function text($name,$label)
    {
        return $this->field([
            'name'=>$name,
            'label'=>$label,
            'type'=>'text'
        ]);
    }

    public function file($name,$label)
    {
        return $this->field([
            'name'=>$name,
            'label'=>$label,
            'type'=>'file'
        ]);
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