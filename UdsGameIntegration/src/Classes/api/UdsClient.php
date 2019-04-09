<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 13.02.2019
 * Time: 14:33
 */

namespace UdsGame\Classes\api;


class UdsClient
{
    const AZ_CHILD_CASHIER = 'child_cashier';
    const AZ_MAIN_CASHIER = 'main_cashier';
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
                throw new \Exception('Error message');
        }
    }


    /**
     * @param $apiKey
     * @param $action
     * @param null $params
     * @return UdsClient
     * @throws \Exception
     */
    public static function getInstance($apiKey, $action, $params = null)
    {
        return new static($apiKey, $action, $params);
    }

    /**
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
     /*   $params = http_build_query([
            'key' => $params['key']
        ]);*/

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