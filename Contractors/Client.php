<?php

namespace v3\Contractors;

class Client extends Contractor
{
    public function __construct(int $resellerId)
    {
        // какой-нибудь SELECT
        // example
        $this->id = 3;
        $this->name = 'Michael';
        $this->type = 0;
    }
    public function getSellerId(){
        // какое-то условие связи

        return 555;
    }

}