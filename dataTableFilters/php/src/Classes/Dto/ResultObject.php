<?php
/**
 *
 */

namespace DataTableFilters\Classes\Dto;

/**
 * ДТО для результа запроса с пагинацией
 *
 * Class ResultObject
 * @package DataTableFilters\Classes\Dto
 *
 * @param array $items
 * @param int $total
 * @param int $draw
 * @param array $columns
 *
 */
class ResultObject extends AbstractDto
{
    protected $items;
    protected $total;
    protected $draw;
    protected $columns;

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
