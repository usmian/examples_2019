<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 13.02.2019
 * Time: 14:07
 */

namespace UdsGame\Classes\api;


use UdsGame\Contracts\Request as IRequest;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class UdsRequest implements IRequest
{
    const URL_CUSTOMER = 'https://udsgame.com/v1/partner/customer';
    const URL_COMPANY = 'https://udsgame.com/v1/partner/company';
    const URL_PURCHASE = 'https://udsgame.com/v1/partner/purchase';
    const URL_REVERT = 'https://udsgame.com/v1/partner/revert';

    private $apiKey;
    private $date;
    private $uuid;
    private $url;

    /**
     * UdsRequest constructor.
     * @param $apiKey
     * @throws \Exception
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->date = new \DateTime();
        try {
            $uuid4 = Uuid::uuid4();
        } catch (UnsatisfiedDependencyException $e) {
            return 'Caught exception: ' . $e->getMessage() . "\n";
        }
        $uuid = $uuid4->toString();
        $this->uuid = $uuid;
    }

    public function send($action, $method, $data = null)
    {
        $this->url = $this->getUrl($action);
        return $this->action($method, $data);
    }

    /**
     * @param $action
     * @return mixed
     */
    private function getUrl($action)
    {
        $urls = [
            'customer' => self::URL_CUSTOMER,
            'company' => self::URL_COMPANY,
            'purchase' => self::URL_PURCHASE,
            'revert' => self::URL_REVERT
        ];
        return $urls[$action];
    }

    /**
     * @param $method
     * @param $data
     * @return mixed
     */
    private function action($method, $data = null)
    {

        if ($method == 'POST') {
            $result = $this->responsePost($data);
        } else {
            $opts = $this->getOpts($method, $data);
            $result = $this->response($opts);
        }

        return $result;
    }

    /**
     * @param $method
     * @param $data
     * @return array
     */
    private function getOpts($method, $data)
    {

        $opts = array(
            'http' => array(
                'method' => "$method",
                'header' => "Accept: application/json\r\n" .
                    "Accept-Charset: utf-8\r\n" .
                    "X-Api-Key: " . $this->apiKey . "\r\n" .
                    "X-Origin-Request-Id: " . $this->uuid . "\r\n" .
                    "X-Timestamp: " . $this->date->format(\DateTime::ATOM)
            )
        );



        if (!empty($data) && $method == 'GET') {
            $this->url .= '?' . $data;
        }

        return $opts;
    }


    private function responsePost($data)
    {
        if (!empty($data)) {
            //$opts['http']['content']=$data;

                $headers = [
                    "Accept: application/json",
                    "Accept-Charset: utf-8",
                    "X-Api-Key: " . $this->apiKey,
                    "X-Origin-Request-Id: " . $this->uuid,
                    "X-Timestamp: " . $this->date->format(\DateTime::ATOM)
                ];
                $ch = curl_init($this->url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                $response = curl_exec($ch);

                if (curl_error($ch)) {
                    trigger_error('Curl Error:' . curl_error($ch));
                }

                curl_close($ch);

                return json_decode($response, true);

        }
        return false;
    }

    /**
     * @param $opts
     * @return mixed
     */
    private function response($opts)
    {

        $context = stream_context_create($opts);
        $result = @file_get_contents($this->url, false, $context);
        if ($http_response_header[0] != 'HTTP/1.1 200') {
            $error = $http_response_header[0];
        }
        if ($result === false) {
            return 'not_found';
        };
        return json_decode($result);
    }


}