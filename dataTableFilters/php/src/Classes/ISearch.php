<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 20.09.2018
 * Time: 22:07
 */
namespace DataTableFilters\Classes;

use DataTableFilters\Classes\dto\ResultObject;

interface ISearch
{
    public function search($aAssocData, $iLimit, $iOffset);
    public function prepareResponse($data, $draw);
}