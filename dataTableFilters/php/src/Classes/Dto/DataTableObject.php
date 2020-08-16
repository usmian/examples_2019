<?php

namespace DataTableFilters\Classes\Dto;

/**
 * ДТО для дататэйбл
 *
 * Class DataTableObject
 * @package DataTableFilters\Classes\Dto
 */
class DataTableObject extends AbstractDto
{
    protected $columns;
    protected $data;
    protected $limit;
    protected $offset;
    protected $draw;
    protected $recordsTotal;
    protected $recordsFiltered;
    protected $success;

    public function toArray(): array
    {
        return [
            'items' => $this->items,
            'total' => $this->total,
            'draw' => $this->draw,
            'columns' => $this->columns,
        ];
    }
}