<?php
/**
 * Сервис для обработки табличной модели
 */

namespace DataTableFilters\Classes;

use DataTableFilters\Classes\dto\DataTableObject;

class FilterService
{
    /**
     * @var ISearch
     */
    private $filtersModel;

    /**
     * Model constructor.
     * @param ISearch $filtersModel
     */
    public function __construct(ISearch $filtersModel)
    {
        $this->filtersModel = $filtersModel;
    }

    /**
     * @param $aAssocData
     * @param DataTableObject $dtObject
     * @return mixed
     */
    public function getData(array $aAssocData, DataTableObject $dtObject)
    {
        /** фильтр от пустых значений */
        $aAssocData = array_filter($aAssocData, function ($mValue) {
            return !empty($mValue);
        });

        ##  Передаем данные в search model
        $data = $this->filtersModel->search($aAssocData, $dtObject->limit, $dtObject->offset);
        ##  Получаем подготовленные для DataTable данные
        return $this->filtersModel->prepareResponse($data, $dtObject->draw);
    }

}