<?php

namespace App\CommandBus\Auth\Register;

use App\CommandBus\CommandInterface;
use App\Components\AbstractDto;

/**
 * Публичная регистрация пользователя
 * Class Command
 *
 * @property $login
 * @property $password
 * @prropery $email
 */
class Command extends AbstractDto implements CommandInterface
{
    protected string $login;
    protected string $password;
    protected string $email;

    public function toArray(): array
    {
        return [
            'login' => $this->login,
            'password' => $this->password,
            'email' => $this->email,
        ];
    }
}
