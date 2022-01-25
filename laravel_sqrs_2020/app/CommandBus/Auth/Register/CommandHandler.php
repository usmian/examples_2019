<?php

namespace App\CommandBus\Auth\Register;

use App\CommandBus\AbstractCommandHandler;
use App\CommandBus\CommandInterface;
use App\Exceptions\CommandException;
use App\Exceptions\DbException;
use App\ModelManagers\UsersManager;
use Illuminate\Mail\Mailer;

/**
 * Публичная Регистрация пользователя
 * - Присваиваем роль USER_ROLE
 * - Пишем в базу
 * - Отправляем письмо
 * - Ждем подтверждения
 *
 * Class CommandHandler
 *
 * @package App\CommandBus\Auth\Register
 */
class CommandHandler extends AbstractCommandHandler
{
    /**
     * @var UsersManager
     */
    protected UsersManager $usersManager;

    protected Mailer $mailer;

    public function __construct()
    {
        $this->usersManager = resolve(UsersManager::class);
        $this->mailer = resolve(Mailer::class);
    }

    /**
     * @param CommandInterface $command
     * @return void
     * @throws CommandException
     * @throws DbException
     */
    public function __invoke(CommandInterface $command): void
    {
        if (!$command instanceof Command) {
            throw new CommandException('Передан неверный класс команды в обработчик' . self::class);
        }
        $this->command = $command;
        /** Создание аккаунта пользователя */
        if (!$this->usersManager->createUserAccount($this->command)) {
            throw new DbException('Ошибка БД при создании аккаунта пользователя');
        };
        /**  отправление письма */
        //$this->mailer->send($this->command);
    }
}
