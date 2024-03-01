<?php

namespace v3\Operations;

class Status
{
    static array $status = [
        0 => 'Completed',
        1 => 'Pending',
        2 => 'Rejected',
    ];

    public static function getName(int $id): string
    {
        return self::$status[$id];
    }
}