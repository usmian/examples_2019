<?php
namespace App\QueryBus\User\Find;

use App\ModelManagers\UsersManager;
use App\QueryBus\QueryInterface;

/**
 * Запрос на поиск по токену
 *
 * Class UserFindByTokenQuery
 * @package App\QueryBus\UserController\Find
 *
 */
class UserFindByTokenQuery implements QueryInterface
{
    protected string $token;

    /**
     * @var UsersManager
     */
    protected UsersManager $usersManager;

    /**
     * UserFindByTokenQuery constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
        $this->usersManager = resolve(UsersManager::class);
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        return $this->usersManager->finOneByToken($this->token);
    }
}
