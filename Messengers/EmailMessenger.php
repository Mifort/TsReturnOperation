<?php

namespace v3\Messengers;

class EmailMessenger extends AbstractMessenger
{
    public function send($data): bool
    {
        return true;
    }
}