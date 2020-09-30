<?php


namespace App\Exceptions;


class CommandException extends \LogicException
{
    public function __construct(string $message = 'ошибка runtime', int $code = 500)
    {
        $httpStatusCode = 500;
        parent::__construct($httpStatusCode, $message, $code);
    }
}
