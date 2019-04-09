<?php
/**
 * Created by PhpStorm.
 * User: usikov
 * Date: 07.11.2017
 * Time: 11:11
 */

namespace app\modules\mtsgps\services;

use yii\httpclient\Client;

trait YaCoordinatesTrait
{
    public static function getCoordinates($data)
    {
        $httpClient = new Client();

        $response = $httpClient->createRequest()
            ->setMethod('get')
            ->setFormat(Client::FORMAT_URLENCODED)
            ->setUrl('https://geocode-maps.yandex.ru/1.x/')
            ->setData(['format' => 'json', 'geocode' => $data])
            ->send();

        if ($response->isOk) {
            $response = $response->getContent();
            $res = json_decode($response);
            return $res;
        } else {
            return false;
        }

    }
}