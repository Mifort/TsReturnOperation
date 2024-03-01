<?php

namespace v3\Messengers\Interfaces;

interface MessengerInterface
{
//    public function setSender($value): MessengerInterface;
    public function send($data):bool;
}