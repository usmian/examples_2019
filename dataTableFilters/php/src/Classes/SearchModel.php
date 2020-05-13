<?php
/**
 * Абстрактный класс табличной модели
 */

namespace DataTableFilters\Classes;

abstract class SearchModel
{
    const EQUAL = 1;
    const IN = 2;
    const MTE = 3;// >=
    const LTE = 4;// <=

    /**
     * @var $sql \MySQL
     * @get
     * @set
     */
    protected $sql;
    protected $draw;
    protected static $filters = [];

    ## в этой функции пишем запрос к бд
    abstract protected function executeQuery($sJoin, $sWhere, $iLimit, $iOffset, $params);

    ## если необходимо преобразовываем полученные данные
    abstract protected function mapData(Array $data);

    /**
     * @param $aAssocData
     * @return string
     */
    ## извлекаем фильтр из массива фильтров, если название фильтра не совпадает с полем в БД, то прописываем transform
    protected function extractFilter($aAssocData)
    {
        $sWhere = '';
        foreach ($aAssocData as $key => $data) {
            $exp = '';

            switch (static::$filters[$key]['condition']) {
                case self::EQUAL :
                    $exp = '=';
                    break;
                case self::IN :
                    $exp = 'IN (' . implode(',', $data) . ')';
                    break;
                case self::MTE :
                    $exp = '>=';
                    break;
                case self::LTE :
                    $exp = '<=';
                    break;
            }


            $condition = '';
            $condition .= $this->prepareCondition($condition, $key);

            $condition .= $exp;

            $data = !empty(static::$filters[$key]['callback'])
                ? $this->{static::$filters[$key]['callback']}($data)
                : $data;

            $data = !empty(static::$filters[$key]['quotes']) ? '\'' . $data . '\'' : $data;
            $condition .= ($data == 'on') ? 1 : $data;

            $sWhere .= $condition;
        }
        return $sWhere;
    }

    /**
     * Подготавливаем запрос
     *
     * @param $condition
     * @param $key
     * @return string
     */
    private function prepareCondition($condition, $key)
    {

        $condition .= ' AND ';
        $callback = !empty(static::$filters[$key]['callback_sql']) ? true : false;
        $condition .= $callback
            ? static::$filters[$key]['sql_start'] . static::$filters[$key]['alias']
            : static::$filters[$key]['alias'];

        $condition .= !empty(static::$filters[$key]['transform'])
            ? static::$filters[$key]['transform']
            : $key;
        $condition .= $callback ? static::$filters[$key]['sql_end'] : '';
        return $condition;
    }

    /**
     * Формат даты
     *
     * @param $date
     * @return string
     */
    private function formatDateYearMonth($date)
    {
        $new = (\DateTime::createFromFormat('m.Y', $date));
        $result = $new->format('Y-m');
        return $result;
    }
}