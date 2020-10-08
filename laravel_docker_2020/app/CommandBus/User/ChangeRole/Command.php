<?php

namespace App\CommandBus\User\ChangeRole;

use App\CommandBus\CommandInterface;
use App\Components\AbstractDto;

/**
 * Публичная регистрация пользователя
 * Class Command
 *
 * @property array $roles
 */
class Command extends AbstractDto implements CommandInterface
{
    protected array $roles;
    //protected string $phone;

    public function toArray(): array
    {
        return [
            'roles' => $this->roles,
        ];
    }
}