<?php

namespace App\Http\Controllers\Auth;

use App\CommandBus\CommandBus;
use App\CommandBus\Auth\Register\Command as RegisterCommand;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\ModelManagers\UsersManager;
use App\QueryBus\QueryBus;
use App\QueryBus\User\Find\UserFindByTokenQuery;
use Illuminate\Support\Facades\DB;

/**
 * Class RegisterController
 * @package App\Http\Controllers
 */
class RegisterController extends Controller
{
    /**
     * @var CommandBus $commandBus
     *
     * - Команды пробрасываются через шину - достаточно разложить по папочкам и задать неймспейсы. глядеть: @see CommandBus
     * - Command Bus для самых маленьких
     * - Упорядочивает бизнес-логику, но на каждый чих надо делать команду и обработчик. Мне нравится
     * ----------------Педальный CQRS
     */
    protected CommandBus $commandBus;

    /**
     * @var QueryBus $queryBus
     *
     * - Запросы пробрасываются через шину - чисто для примера, так как здесь в принципе хватит ModelManagers @see UsersManager
     * - Query Bus для самых маленьких
     * - Каждый запрос на получение данных - отдельный класс
     *  ----------------Педальный CQRS
     */
    protected QueryBus $queryBus;

    /**
     * RegisterController constructor.
     */
    public function __construct()
    {
        $this->commandBus = resolve(CommandBus::class);
        $this->queryBus = resolve(QueryBus::class);
    }

    /**
     * Публичная регистрация пользователя
     *
     * @param RegistrationRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function registration(RegistrationRequest $request)
    {
        /** Радостно оборачиваем все в транзакцию потому что должно отработать единым блоком */
        DB::transaction(function () use ($request) {
            /** Все уже отвалидированное пихаем в обработчик команды сквозь шинку  */
            $this->commandBus->handle(RegisterCommand::fromArray($request->all()));
        });

        /** И да - это для упрошения */
        return response('Письмо с потдверждением отправлено');
    }

    /**
     * Получение пользователя по токену
     *
     * @param string $token
     */
    public function getUser(string $token)
    {
        // Чисто для примера
        return $this->queryBus->query(new UserFindByTokenQuery($token));
    }
}
