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
 * @property string $email
 * @property string $phone
 */
class Command extends AbstractDto implements CommandInterface
{
    protected string $name;
    protected string $password;
    protected string $email;
    //protected string $phone;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'password' => $this->password,
            'email' => $this->email,
            //'phone'=> $this->phone,
        ];
    }
}
