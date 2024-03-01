<?php

namespace v3\Contractors;

class Employee extends Contractor
{
    public function __construct(int $resellerId)
    {
        $this->id = 4;
        $this->name = 'John';
        $this->type = 0;
        // проверяем если такой например в бд
        // если есть
    }
}