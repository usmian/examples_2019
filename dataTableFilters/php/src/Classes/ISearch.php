<?php
/**
 * Интерфейс табличной модели
 */
namespace DataTableFilters\Classes;

use DataTableFilters\Classes\dto\ResultObject;

interface ISearch
{
    public function search($aAssocData, $iLimit, $iOffset);
    public function prepareResponse($data, $draw);
}