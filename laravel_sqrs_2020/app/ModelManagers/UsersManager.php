<?php

namespace App\ModelManagers;

use App\CommandBus\Auth\Register\Command as RegisterCommand;
use App\Components\AbstractModelManager;
use App\Definitions\UserRoleDefinition;
use App\Models\User;

/**
 * Class UsersManager
 * @package App\ModelManagers
 */
class UsersManager extends AbstractModelManager
{
    /**
     * @return string
     */
    public function getModelName(): string
    {
        return User::class;
    }

    /**
     * @param RegisterCommand $registerCommand - команда(dto) регистрации пользователя
     * @param array $roles роль(и) для создаваемого юзера (При обычной регистрации - присваиваем роль обычного пользователя)
     * @return bool
     */
    public function createUserAccount(RegisterCommand $registerCommand, array $roles = [UserRoleDefinition::USER_ROLE]): bool
    {
        $user = new User();
        /** Вручную распаковываем Dto, чтобы захэшировать пароль */
        $user->fill([
            'name' => $registerCommand->name,
            'password' => password_hash($registerCommand->password, PASSWORD_DEFAULT),
            'email' => $registerCommand->email,
            'roles' => $roles // Можно присвоить сразу несколько ролей. Проверка на валидность - снаружи
            // TODO: запилить телефон с подтверждением
            //'phone' => $registerCommand->phone,
            //'phone_formatted'=> PhoneHelper::formatToInt($registerCommand->phone),
        ]);
        return $user->save();
    }

    /**
     * @param string $token
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function finOneByToken(string $token): User
    {
        return $this->byAttributes([
            'token' => $token,
        ])->firstOrFail();
    }
}
