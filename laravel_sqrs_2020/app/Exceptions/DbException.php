<?php


namespace App\Exceptions;


class DbException extends \Exception
{
    public function __construct(string $message = 'ошибка БД', int $code = 500)
    {
        $httpStatusCode = 500;
        parent::__construct($httpStatusCode, $message, $code);
    }
}
