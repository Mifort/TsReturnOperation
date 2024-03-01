<?php

namespace v3\Messengers;

use v3\Messengers\Interfaces\MessengerInterface;

class Messenger implements MessengerInterface
{
    private object $messenger;
    protected static string $emailFrom = 'contractor@example.com';


    public function toEmail(): object
    {
        $this->messenger = new EmailMessenger();
        return $this;
    }
    public function toSms(): object
    {
        $this->messenger = new SmsMessenger();
        return $this;
    }
    public static function getResellerEmailFrom(): string
    {
        return self::$emailFrom;
    }

    public function send($data):bool{
        return $this->messenger->send($data);
    }
}