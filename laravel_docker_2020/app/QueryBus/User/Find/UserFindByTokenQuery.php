<?php
namespace App\QueryBus\User\Find;

use App\ModelManagers\UsersManager;
use App\QueryBus\QueryInterface;

/**
 * Запрос на поиск по токену
 *
 * Class UserFindByTokenQuery
 * @package App\QueryBus\User\Find
 *
 */
class UserFindByTokenQuery implements QueryInterface
{
    protected string $token;

    /**
     * UserFindByTokenQuery constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * @var UsersManager
     */
    protected UsersManager $usersManager;

    /**
     * @return mixed
     */
    public function __invoke()
    {
        $this->usersManager = resolve(UsersManager::class);
        return $this->usersManager->finOneByToken($this->token);
    }
}
