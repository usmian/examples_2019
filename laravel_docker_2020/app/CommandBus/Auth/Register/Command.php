<?php

namespace App\CommandBus\Auth\Register;

use App\CommandBus\CommandInterface;
use App\Components\AbstractDto;

/**
 * Публичная регистрация пользователя
 * Class Command
 *
 * @property string $name
 * @property string $password
 * @prropery string $email
 */
class Command extends AbstractDto implements CommandInterface
{
    protected string $name;
    protected string $password;
    protected string $email;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'password' => $this->password,
            'email' => $this->email,
        ];
    }
}
