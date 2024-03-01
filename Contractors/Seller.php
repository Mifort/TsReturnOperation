<?php

namespace v3\Contractors;

class Seller extends Contractor
{
    public function __construct(int $resellerId)
    {
        // какой-нибудь SELECT
        $this->id = 2;
        $this->name = 'Tom';
        $this->type = 0;
    }
    function getEmailsByPermit( $event): array
    {
        // fakes the method
        return ['someemeil@example.com', 'someemeil2@example.com'];
    }
}