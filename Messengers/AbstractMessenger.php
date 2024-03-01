<?php

namespace v3\Messengers;

use v3\Messengers\Interfaces\MessengerInterface;

class AbstractMessenger implements MessengerInterface
{
    protected object $sender;

    public function send($data): bool
    {
        return true;
    }
}