<?php

namespace App\CommandBus;

use App\CommandBus\CommandInterface;

/**
 * Командная шина
 * - Все проходит через одну командную шину и не нужно мучаться с собиранием кучи сервисов
 * - Получает класс команды(dto) и ее обработчик
 * - Обработчик лежит в одном неймспейсе с командой
 * - Разруливаем все зависимости через DI контейнер
 *
 * Class CommandBus
 * @package App\CommandBus
 */
class CommandBus
{
    /**
     * Литерал хэндлера команды
     */
    private const HANDLER_NAME = 'Handler';

    /**
     * Общий хэндлер для всех команд
     *
     * @param \App\CommandBus\CommandInterface $command
     * @return
     */
    public function handle(CommandInterface $command): void
    {
        $commandHandlerClassName = get_class($command) . self::HANDLER_NAME;
        /**
         * @var AbstractCommandHandler $handler
         */
        $commandHandler = app($commandHandlerClassName);
        $commandHandler($command);
    }
}
