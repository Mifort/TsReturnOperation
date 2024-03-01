<?php

namespace v3\Contractors;

class Contractor
{
    const TYPE_CUSTOMER = 0;

    static $instance;
    protected int $id;
    protected int $type;
    protected string $name;


    protected function __construct(int $resellerId)
    {
        // какой-нибудь SELECT
        // по каким-то условиям сетим type , например 5
    }
    public static function getById(int $resellerId)
    {
            static::$instance = new static($resellerId);
            if(static::$instance->id === null)
                static::$instance = null;
        return static::$instance;
    }
    public function getType(){
        return $this->type;
    }
    public function getId(){
        return $this->id;
    }
    public function getFullName(): string
    {
        return $this->name . ' ' . $this->id;
    }

}