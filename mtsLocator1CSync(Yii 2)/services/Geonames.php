<?php

/**
 * Created by PhpStorm.
 * User: usikov
 * Date: 10.08.2017
 * Time: 12:29
 */
/**
 * @var $res \yii\httpclient\Client;
 */

namespace app\modules\mtsgps\services;

use yii\httpclient\Client;
use yii\web\HttpException;


class Geonames
{
    private $res;

    public function __construct($flag = 'object')
    {
        switch ($flag) {
            case 'object':
                $this->prepareObjects();
                break;
            case 'subscriber':
                $this->prepareSubscribers();
                break;
        }
    }

    public static function setTimeZone($id)
    {
        $object = Objects::find()
            ->where(['id_in_mts' => $id])
            ->one();
        $lng = $object->longitude;
        $lat = $object->latitude;
        //get data from GeoNames depends on lng & lat
        $res = self::requestCoords($lng, $lat);
        //update timezones in time mode table
        if ($res->isOk) {
            $content = $res->getContent();
            $content = json_decode($content);
            $offset = $content->gmtOffset;
            $timezone = $content->timezoneId;
            TimeModeStorage::updateTimezones($id, $offset, $timezone);
        }
    }

    public static function setTimeZones()
    {
        $objects = Objects::find()
            ->with('timeMode')
            ->all();
        //every obj
        foreach ($objects as $object) {
            $lng = $object->longitude;
            $lat = $object->latitude;
            //get data from GeoNames depends on lng & lat
            $res = self::requestCoords($lng, $lat);

            //update timezones in time mode table
            if ($res->isOk) {
                $content = $res->getContent();
                $content = json_decode($content);
                $offset = $content->gmtOffset;
                $timezone = $content->timezoneId;
                $tm = $object->timeMode;
                $id = $tm->id_in_mts;

                TimeModeStorage::updateTimezones($id, $offset, $timezone);
            } else {
                throw new HttpException(' error getting timezone from Geonames ');
            }
        }
    }


    private function prepareObjects()
    {
        //actual objects and tms
        $objects = ObjectsOld::find()
            ->with('timeMode')
            ->all();
        //every obj
        foreach ($objects as $object) {
            $lng = $object->longitude;
            $lat = $object->latitude;
            //get data from GeoNames depends on lng & lat
            $this->request($lng, $lat);
            $res = $this->res;
            //update timezones in time mode table
            if ($res->isOk) {
                $content = $res->getContent();
                $content = json_decode($content);
                $offset = $content->gmtOffset;
                $timezone = $content->timezoneId;
                $tm = $object->timeMode;
                $id = $tm->id_in_mts;
                TimeModeStorage::updateTimezones($id, $offset, $timezone);
            } else {
                throw new HttpException(' error getting timezone from Geonames ');
            }
        }
    }

    private function prepareSubscribers()
    {
        $req = new SoapModel(SoapModel::BTB, 'callGetSubscribers', [], '1');
        $subs = $req->result;

        $subscriberLRs = [];
        foreach ($subs as $subscriber) {
            $subscriberLRs[$subscriber->ID] = $subscriber->LastSuccessfulRequestID;
        }
        foreach ($subscriberLRs as $key => $lR) {
            if ($lR != -1) {
                $tm = Subscribers::findOne(['id_in_mts' => $key]);
                if (empty($tm->timezone)) {
                    $sub = new SoapModel(SoapModel::BTB, 'callGetRequest', ['id' => $lR], '1');
                    $res = $sub->result->GetRequestResult;
                    $lng = $res->Longitude;
                    $lat = $res->Latitude;
                    $this->request($lng, $lat);
                    if ($this->res->isOk) {
                        $content = $this->res->getContent();
                        $content = json_decode($content);
                        $offset = $content->gmtOffset;
                        $timezone = $content->timezoneId;
                        $id = $key;
                        SubscribersStorage::updateTimezones($id, $offset, $timezone);
                    } else {
                        throw new HttpException(' error getting timezone from Geonames ');
                    }
                }

            }
        }
    }

    private function request($lng, $lat)
    {
        $httpClient = new Client();
        //30000 responses per day is a limit geonames.org(!warning!achtung!)
        $response = $httpClient->createRequest()
            ->setMethod('get')
            ->setFormat(Client::FORMAT_URLENCODED)
            ->setUrl('http://api.geonames.org/timezoneJSON')
            ->setData(['lat' => $lat, 'lng' => $lng, 'username' => 'usmian'])
            ->send();
        $this->res = $response;
    }

    private static function requestCoords($lng, $lat)
    {
        $httpClient = new Client();
        //30000 responses per day is a limit geonames.org(!warning!achtung!)
        $response = $httpClient->createRequest()
            ->setMethod('get')
            ->setFormat(Client::FORMAT_URLENCODED)
            ->setUrl('http://api.geonames.org/timezoneJSON')
            ->setData(['lat' => $lat, 'lng' => $lng, 'username' => 'usmian'])
            ->send();
        return $response;
    }

}

/*
 * SubscriberTariffTypeID = 7
LastRequestID = -1
LastSuccessfulRequestID = -1
Algorithm = 8
Icon = 0
CanTrack = true
CanRequest = true
CanSend = true
ID = 1503985
GroupID = 190841
Name = "Пашин Артур"
Nick = "25"
IsOnline = false
ExternalSubscriberID = ""
IsCoordinatorAvailable = false
IsLocateEnabled = false
 * */
//last_successful_request_id
//timezone
//timezone_str