<?php

namespace UdsGame\Classes\api;

/**
 * Class UdsClient
 *
 * Клиент для работы с UdsGame
 *
 * @package UdsGame\Classes\api
 * @author usikov.m usmian@yandex.ru
 */
class UdsClient
{
    /**
     * @const 1-я клиника
     */
    const AZ_MAIN_CASHIER = 'main_cashier';

    /**
     * @const 2-я клиника
     */
    const AZ_CHILD_CASHIER = 'child_cashier';

    /**
     * @const московская клиника
     */
    const AZ_MOSCOW_CASHIER = 'moscow_cashier';

    private $apiKey;
    private $response;

    public function __construct($apiKey, $action, $params)
    {
        $this->apiKey = $apiKey;

        switch ($action) {
            case 'customer':
                $this->requestCustomerInfo($params);
                break;
            case 'company':
                $this->requestCompanyInfo();
                break;
            case 'purchase':
                $this->postPurchase($params);
                break;
            case 'revert':
                $this->postRevert($params);
                break;
            default:
                throw new \Exception('Неверное наименование запроса: ' . $action);
        }
    }

    /**
     * Получить экземпляр клиента
     *
     * @param $apiKey
     * @param $action
     * @param null $params
     * @return UdsClient
     * @throws \Exception
     */
    public static function getInstance($apiKey, $action, $params = null): UdsClient
    {
        return new static($apiKey, $action, $params);
    }

    /**
     * тело ответа
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param $params
     * @throws \Exception
     */
    private function requestCustomerInfo($params)
    {

        if (isset($params['code'])) {
            $params = 'code=' . urlencode($params['code']);
        }
        if (isset($params['phone'])) {
            $params = 'phone=' . urlencode($params['phone']);
        }
        if (empty($params)) {
            throw new \Exception('need client param(code or phone)');
        }

        $response = (new UdsRequest($this->apiKey))
            ->send('customer', 'GET', $params);
        $this->response = $response;
    }


    /**
     * @throws \Exception
     */
    private function requestCompanyInfo()
    {
        $response = (new UdsRequest($this->apiKey))
            ->send('company', 'GET');

        $this->response = $response;
    }

    /**
     * @param $params
     * @throws \Exception
     */
    private function postPurchase($params)
    {
        $response = (new UdsRequest($this->apiKey))
            ->send('purchase', 'POST', $params);
        $this->response = $response;
    }

    /**
     * @param $params
     * @throws \Exception
     */
    private function postRevert($params)
    {
        $response = (new UdsRequest($this->apiKey))
            ->send('revert', 'POST', $params);
        $this->response = $response;
    }
}
