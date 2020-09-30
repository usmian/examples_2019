<?php

namespace App\CommandBus;

/**
 * Class AbstractCommandHandler
 *
 * @property $command
 * @package App\CommandBus
 */
abstract class AbstractCommandHandler
{
    /**
     * @var CommandInterface $command
     */
    protected CommandInterface $command;

    /**
     * @param CommandInterface $command
     */
    abstract public function __invoke(CommandInterface $command);
}
