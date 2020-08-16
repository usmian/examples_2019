<?php

namespace DataTableFilters\modules\dashboard;

use DataTableFilters\Classes\dto\DataTableObject;
use DataTableFilters\Classes\dto\ResultObject;
use DataTableFilters\Classes\ISearch;
use DataTableFilters\Classes\SearchModel;

class Log3SearchModel extends SearchModel implements ISearch
{
    const REDIRECT_PERCENT = 0.05;

    private $dates;
    ##
    ## Дата
    protected static $filters = [
        'date_from' => [
            'alias' => 'a.',
            'transform' => 'date',
            'quotes' => true,
            'condition' => self::MTE
        ],
        'date_to' => [
            'alias' => 'a.',
            'transform' => 'date',
            'quotes' => true,
            'condition' => self::LTE
        ],
        'id_clinic' => [
            'alias' => 'a.',
            'condition' => self::EQUAL
        ],
    ];


    /**
     * LogSearchModel constructor.
     * @param $sql \MySQL
     * @param $draw
     */
    public function __construct(\MySQL $sql, $draw)
    {
        $this->sql = $sql;
        $this->draw = $draw;
    }


    /**
     * @param $aAssocData
     * @param int $iLimit
     * @param int $iOffset
     * @return array
     * @throws \Exception
     */
    public function search($aAssocData, $iLimit, $iOffset)
    {
        $sWhere = $this->extractFilter($aAssocData);
        $sJoin = '';
        return $this->executeQuery($sJoin, $sWhere, $iLimit, $iOffset, $aAssocData);
    }

    /**
     *
     * @param string $sJoin
     * @param string $sWhere
     * @param $iLimit
     * @param $iOffset
     * @param $params
     * @return array|ResultObject
     */
    public function executeQuery($sJoin = '', $sWhere = '', $iLimit, $iOffset, $params)
    {
        $data = new ResultObject();
        ## список дат для формирования таблицы
        $this->dates = $this->getDates($params);
        try {
            $sQuery = '
			SELECT SQL_CALC_FOUND_ROWS
				 a.id_clinic,
				GROUP_CONCAT(CAST(a.id_doctor AS CHAR) SEPARATOR \'#\') AS id_doctors,
				GROUP_CONCAT(CAST(DATE_FORMAT(a.date, "%d.%m.%Y") AS CHAR) SEPARATOR \'#\') AS dates_rus,
				GROUP_CONCAT(CAST(a.date AS CHAR) SEPARATOR \'#\') AS dates,
				GROUP_CONCAT(CAST(b.discount AS CHAR) SEPARATOR \'#\') AS discounts,
				GROUP_CONCAT(CAST(b.discount2 AS CHAR) SEPARATOR \'#\') AS discounts2, 
				GROUP_CONCAT(CAST(b.price AS CHAR) SEPARATOR \'#\') AS prices, 
				GROUP_CONCAT(CAST(b.refund AS CHAR) SEPARATOR \'#\') AS refunds, 
				GROUP_CONCAT(CAST(c.commission_cash AS CHAR) SEPARATOR \'#\') AS commissions, 
				GROUP_CONCAT(CAST(c.commission_percent AS CHAR) SEPARATOR \'#\') AS commissions_percents, 
				b.id_doctor_assigned,
				f.title,
		        CONCAT_WS(" ", e.lastname , e.firstname, e.middlename) AS doctor_fio,
                g.id, 
                CONCAT_WS(" ", g.lastname, g.firstname, g.middlename) AS assigned_fio
			FROM 
				' . DB_TABLE_PATIENT_FILE . ' AS a, 
				' . DB_TABLE_SERVICE_PATIENT_FILE . ' AS b LEFT OUTER JOIN ' . DB_TABLE_PEOPLES . ' AS g ON g.id = b.id_doctor_assigned,
				' . DB_TABLE_SERVICES . ' AS c,
				' . DB_TABLE_PATIENTS . ' AS d,
				' . DB_TABLE_PEOPLES . ' AS e,
				' . DB_TABLE_PROFILE . ' AS f
 			WHERE
				    b.id_patient_file = a.id 
				AND 
				    b.id_cash_type NOT IN (1, 4, 8) 
				AND
				    c.id = b.id_service 
				AND
				    d.id = a.id_patient 
				AND
                    e.id = a.id_doctor 
                AND
                    f.id = a.id_clinic 
                AND
                    b.id_doctor_assigned > 0
				' . $sWhere . '
				GROUP BY b.id_doctor_assigned
                ORDER BY a.id_clinic, e.lastname ASC
		';
            //LIMIT ' . $iLimit . ' OFFSET ' . $iOffset . '
            $this->sql->query(
                $sQuery
            );

            if ($this->sql->getNumberRows() > 0) {
                $data->result = $this->sql->getFetchObject();
            }

            $this->sql->query('SELECT FOUND_ROWS()');
            if ($this->sql->getNumberRows()) {
                $aResult = reset($this->sql->getFetchArray());
            }
            $data->total = $aResult[0];
        } catch (\Exception $e) {
            $data = ['data' => [],
                'success' => false,
                'error' => $e->getMessage()];
        }
        return $data;
    }

    /**
     * @param $data
     * @param $draw
     * @return DataTableObject
     */
    public function prepareResponse($data, $draw)
    {
        if (is_array($data)) {
            return $data;
        }
        $oData = new DataTableObject();
        ## в случае необходимости преобразовывем данные
        $oData->data = !empty($data->result)
            ? $this->mapData($data->result)
            : [];

        $oData->recordsFiltered = $data->total;
        $oData->recordsTotal = $data->total;
        $oData->draw = $draw;
        $oData->success = true;

        return $oData;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function mapData(Array $data)
    {
        $newData = [];
        foreach ($data as $item) {
            $aDoctors = explode('#', $item->id_doctors);
            $aDates = explode('#', $item->dates);
            $aDiscounts = explode('#', $item->discounts);
            $aDiscounts2 = explode('#', $item->discounts2);
            $aPrices = explode('#', $item->prices);
            $aRefunds = explode('#', $item->refunds);
            $aCommissions = explode('#', $item->commissions);
            $aCommissionsPercents = explode('#', $item->commissions_percents);

            $map = [
                'id_doctor_assigned' => $item->id_doctor_assigned,
                'dates' => $aDates,
                'id_doctors' => $aDoctors,
                'discounts' => $aDiscounts,
                'discounts2' => $aDiscounts2,
                'prices' => $aPrices,
                'refunds' => $aRefunds,
                'commissions' => $aCommissions,
                'commissions_percents' => $aCommissionsPercents
            ];
            $dates = $this->mapDate($map);

            //$newItem[$item->date] = $item->clinic_title;
            $immutable = (array)$item;
            $newData[] = array_merge($dates, $immutable);
        }
        return $newData;
    }

    ## приватные функции это кастом для добавления переменного количества столбцов в дататэйбл
    ## и кастомных вычислений

    /**
     * @param array $map
     * @return array|bool
     */
    private function mapDate(Array $map)
    {
        ##
        $mapped = array_flip($this->dates);
        $mapped = array_map(function () {
            return 0;
        }, $mapped);

        foreach ($this->dates as $date) {
            foreach ($map['dates'] as $key => $cdate) {
                if ($cdate == $date) {
                    if ($map['id_doctors'][$key] !== $map['id_doctor_assigned']) {
                        $iServicePrice = floatval($map['prices'][$key]) -
                            ((floatval($map['prices'][$key]) * floatval($map['discounts'][$key])) / 100)
                            - floatval($map['discounts2'][$key])
                            - $map['refunds'][$key];
                        $iSalaryRedirectService = $iServicePrice * self::REDIRECT_PERCENT;

                        $mapped[$date] += $iSalaryRedirectService;
                    }
                }
            }
        }
        return $mapped;
    }

    /**
     * Возвращает массив дат из интервала
     *
     * @param $aAssocData
     * @return string[]
     */
    private function getDates($aAssocData): array
    {
        $from = new \DateTime($aAssocData['date_from']);
        $to = new \DateTime($aAssocData['date_to']);
        $to = $to->modify('+1 day');

        ##DateInterval('P1D') - интервал 1 день
        ##DatePeriod - объект с набором дат с определенным интервалом
        $period = new \DatePeriod($from, new \DateInterval('P1D'), $to);

        ##DatePeriod - Набор дат для интервала например с 1.02 до 2.03 вернет[2020-02-01 .. все даты по порядку до 2020-03-02]
        $arrayOfDates = array_map(
            function ($item) {
                /**
                 * @var $item \DateTime
                 */
                return $item->format('Y-m-d');
            },
            iterator_to_array($period)
        );
        return $arrayOfDates;
    }
}