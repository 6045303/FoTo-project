<?php

abstract class AbstractService
{
    protected static function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }
}
