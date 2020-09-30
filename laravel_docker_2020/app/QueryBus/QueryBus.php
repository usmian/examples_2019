<?php
namespace App\QueryBus;

class QueryBus
{
    /**
     * @todo должен быть queryHandler для query dto - just do it
     *
     * Вообще собирать всякое можно,например, из редиса, куда кэшируются всякие данные
     *
     * @param QueryInterface $query
     * @return
     */
    public function query(QueryInterface $query): void
    {
        return app($query);
    }
}
