<?php
/**
 * ДТО для дататэйбл
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