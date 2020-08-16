<?php

/**
 * ajax
 * @author starassov
 * @copyright 2014
 *
 * Общий каркас и наименования переменных - это местный codestyle))
 */

session_start();

// -----------------------------------------------------------------------------
//  system wide configuration options
// -----------------------------------------------------------------------------
require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.ini.php';
require_once MODULES_DIR . DS . 'dashboard' . DS . 'dashboard.class.php';
require_once INT_DIR . DS . 'DBconn.class.php';
// -----------------------------------------------------------------------------
//  connect to DB
// -----------------------------------------------------------------------------


try {
    ## filters applied to data table
    $aAssocData=$_POST;

    $commonColumns = [
        ['title' => 'id', 'data' => 'id', 'visible' => false],
        ['title' => 'Клиника', 'data' => 'title', 'width' => '15%'],
        ['title' => 'Ф.И.О', 'data' => 'assigned_fio', 'width' => '15%'],
    ];

    $from = new \DateTime($aAssocData['date_from']);
    $to = new \DateTime($aAssocData['date_to']);
    $to = $to->modify('+1 day');

    ## DateInterval('P1D') - интервал 1 день
    ## DatePeriod - объект с набором дат с определенным интервалом
    $period = new \DatePeriod($from, new \DateInterval('P1D'), $to);

    $arrayOfDates = array_map(
        function ($item) {
            /**
             * @var $item \DateTime
             */
            return ['title' => $item->format('d.m.Y'),
                'data' => $item->format('Y-m-d')];
        },
        iterator_to_array($period)
    );

    $columns = array_merge($commonColumns, $arrayOfDates);

    header('Content-Type: application/json');

    echo json_encode($columns);

} catch (Exception $e) {

    error_log(date('d.m.Y H:i') . ' [error][FrontEnd Ajax File][log3GenerateColumns] ' . $e->getMessage() . "\n", 3, ERROR_LOG_FILE);
}