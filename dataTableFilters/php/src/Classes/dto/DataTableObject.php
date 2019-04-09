<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 26.09.2018
 * Time: 0:29
 */

namespace DataTableFilters\Classes\dto;


class DataTableObject
{
    public $columns;
    public $data;
    public $limit;
    public $offset;
    public $draw;
    public $recordsTotal;
    public $recordsFiltered;
    public $success;
}