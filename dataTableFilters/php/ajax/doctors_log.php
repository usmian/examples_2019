<?php

/**
 * ajax
 * @author starassov
 * @copyright 2014
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


use DataTableFilters\Classes\FilterService;
use DataTableFilters\modules\dashboard\Log3SearchModel;
use DataTableFilters\Classes\dto\DataTableObject;

try {
    /**
     * @var $sql MySQL
     */
    $registry->set('sql', $sql);
    $sql->connect($dbHost, $dbName, $dbUser, $dbPassword);

    // -----------------------------------------------------------------------------
    //  load router object
    // -----------------------------------------------------------------------------


    $dashboard = new Dashboard($registry);
    $registry->set('dashboard', $dashboard);

    //filters applied to data table
    parse_str($_POST['filters'], $aAssocData);

    //data table required params
    $dtObject = new DataTableObject();

    $dtObject->draw = getRequestToDB('draw', 'number', true);
    $dtObject->limit = getRequestToDB('length', 'number', true);
    $dtObject->offset = getRequestToDB('start', 'number', true);

    $oFilter = new FilterService(new Log3SearchModel($sql, $dtObject->draw, 1));
    $dataCurrent = $oFilter->getData($aAssocData, $dtObject);
    $oFilterPrev = new FilterService(new Log3SearchModel($sql, $dtObject->draw, 2));
    $dataPrev = $oFilterPrev->getData($aAssocData, $dtObject);
    $oFilterPrevYear = new FilterService(new Log3SearchModel($sql, $dtObject->draw, 3));
    $dataPrevYear = $oFilterPrevYear->getData($aAssocData, $dtObject);
    $data = $dashboard->mergeData($dataCurrent, $dataPrev->data, $dataPrevYear->data);

    header('Content-Type: application/json');

    echo json_encode($data);

    $sql->close();
} catch (Exception $e) {
    $sql->close();

    error_log(date('d.m.Y H:i') . ' [error][FrontEnd Ajax File][getMkbList] ' . $e->getMessage() . "\n", 3, ERROR_LOG_FILE);
}