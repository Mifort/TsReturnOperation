<?php

namespace v3\Operations;

abstract class ReferencesOperation
{
    abstract public function doOperation(): array;

    public function getRequest($pName): array
    {
        return $_REQUEST[$pName];
    }
}