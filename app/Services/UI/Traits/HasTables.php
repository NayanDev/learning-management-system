<?php

namespace App\Services\UI\Traits;


trait HasTables
{

    protected array $tables = [];


    public function table($table)
    {
        $this->tables[$table->key()] = $table->toArray();

        return $this;
    }

}