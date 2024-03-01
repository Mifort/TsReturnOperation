<?php

namespace v3\Messengers;


class SmsMessenger extends AbstractMessenger
{
    /**
     * @param $data
     * @return bool
     */
    public function send($data): bool
    {
        return true;
    }
}