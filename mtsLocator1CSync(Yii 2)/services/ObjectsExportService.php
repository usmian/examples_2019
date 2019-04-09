<?php
/**
 *
 * @var $model \app\modules\mtsgps\models\MtsObjectsTvk
 * @var $objects \app\modules\mtsgps\models\Objects
 */

namespace app\modules\mtsgps\services;


use app\modules\mtsgps\models\MtsLog;
use app\modules\mtsgps\models\MtsObjectsGroups;
use app\modules\mtsgps\models\MtsObjectsTvk;
use app\modules\mtsgps\models\SoapModel;
use app\modules\mtsgps\models\Objects;
use app\modules\mtsgps\models\TimeMode;
use yii\db\Connection;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class ObjectsExportService
{
    use YaCoordinatesTrait;

    const DIY_REG = 'DIY&HH Рег';
    const DIY_FED = 'DIY&HH ФС';
    private static $messages;

    //
    //filter received from mts
    //
    public static function saveObjects($objects)
    {
        $objects = $objects[0]->dealers;
        foreach ($objects as $object) {
            $channel = $object->SellOutChannel;
            if ($channel == self::DIY_FED || $channel == self::DIY_REG) {
                //if new object in 1C
                $exist = MtsObjectsTvk::isExists($object->id_1С);
                if (!$exist) {
                    $model = new MtsObjectsTvk();
                    $model->id_1c = $object->id_1С;
                    $model->kladr = $object->KLADR;
                    $model->filial = $object->Filial;
                    $model->name = $object->Name;
                    $model->phones = $object->Phones;
                    $model->schedule = $object->Schedule;
                    $model->site = $object->Site;
                    $model->email = $object->Email;
                    $model->selloutchannel = $channel;
                    $model->save(false);
                }
            }
        }
    }

    public static function saveReceivedObjects($object)
    {
        /** @var Connection $db */
        $objects = $object[0]->dealers;
        $objectGroups = MtsObjectsGroups::find()->asArray()->indexBy('mg_name')->all();
        foreach ($objects as $object) {
            $channel = $object->SellOutChannel;
            if ($channel == self::DIY_FED || $channel == self::DIY_REG) {
                //if new object in 1C
                $exist = MtsObjectsTvk::isExists($object->id_1С);
                if (!$exist) {
                    $model = new MtsObjectsTvk();
                    $model->id_1c = $object->id_1С;
                    $model->kladr = $object->KLADR;
                    $model->filial = $object->Filial;
                    $model->name = $object->Name;
                    $model->phones = $object->Phones;
                    $model->schedule = $object->Schedule;
                    $model->site = $object->Site;
                    $model->email = $object->Email;
                    $model->selloutchannel = $channel;
                    $model->save(false);

                    self::addObject($object, $objectGroups, $object->KLADR, $object->Name, $object->Filial);
                }

                //if object rename in 1C
                $rename = MtsObjectsTvk::isNeedRename('name', $object->id_1С, $object->Name);
                if ($rename) {
                    $name = $object->Name;
                    $objectExst = Objects::find()
                        ->where(['id_1c' => $object->id_1С])
                        ->one();
                    $mtsId = $objectExst->id_in_mts;
                    $dataMts = self::getObjectMts($mtsId);

                    $oldAddress = $dataMts->Address;
                    $lng = $dataMts->Longitude;
                    $lat = $dataMts->Latitude;

                    $db = \Yii::$app->get('db3');
                    $transaction = $db->beginTransaction();
                    try {
                        $rename->name = $name;
                        $rename->save();
                        $objectExst->name = $name;
                        $objectExst->latitude = $lat;
                        $objectExst->longitude = $lng;

                        $group = $objectExst->group_id;

                        $addres = !empty($oldAddress) ? $oldAddress : $objectExst->address;
                        $objectExst->address = $addres;
                        /*     $lng = $objectExst->longitude;
                             $lat = $objectExst->latitude;*/

                        $tm = TimeMode::find()
                            ->where(['id_in_mts' => $mtsId])
                            ->one();
                        $tm->object_name = $name;

                        if ($objectExst->save(false) && $tm->save(false)) {
                            self::updateObjectMts($lng, $lat, $rename->name, $addres, $mtsId, $group);
                            //updateObjectsMts throws Exception and transaction rollback
                            $transaction->commit();
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        $log = new MtsLog();
                        $log->id_object = $mtsId;
                        $log->id_1c = $object->id_1С;
                        $log->name_object = $objectExst->name;
                        $log->error_text = 'rename object error';
                    }
                    //TODO: write Exception to Log
                }
                $renameBranch = MtsObjectsTvk::isNeedRename('filial', $object->id_1С, $object->Filial);
                if ($renameBranch) {

                    $objectExst = Objects::find()
                        ->where(['id_1c' => $object->id_1С])
                        ->one();
                    $mtsId = $objectExst->id_in_mts;
                    $dataMts = self::getObjectMts($mtsId);

                    $oldAddress = $dataMts->Address;
                    $lng = $dataMts->Longitude;
                    $lat = $dataMts->Latitude;

                    $db = \Yii::$app->get('db3');
                    $transaction = $db->beginTransaction();
                    try {
                        $renameBranch->filial = $object->Filial;
                        $filialData = ArrayHelper::getValue($objectGroups, $object->Filial);
                        $idGroup = ArrayHelper::getValue($filialData, 'mg_id_mts');
                        $objects = Objects::find()
                            ->where(['id_1c' => $object->id_1С])
                            ->one();
                        $objects->group_name = $object->Filial;
                        $objects->group_id = $idGroup;
                        $addres = !empty($oldAddress) ? $oldAddress : $objects->address;


                        $objects->group_id = $idGroup;
                        if ($renameBranch->save(false) && $objects->save(false)) {
                            self::updateObjectMts($lng, $lat, $renameBranch->name, $addres, $mtsId, $idGroup);
                        }

                        $transaction->commit();
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        $log = new MtsLog();
                        $log->id_object = $mtsId;
                        $log->id_1c = $object->id_1С;
                        $log->name_object = $objectExst->name;
                        $log->error_text = 'rename branch error';
                    }
                }
            }
        }
        self::notify(self::$messages);
    }

    public static function getObjectMts($id)
    {
        $res = new SoapModel(SoapModel::BTB, 'callGetMapObject',
            ['id' => $id], 'test');
        $res = $res->result;
        return $res;
    }
    //
    //create new objects with id 1C and mts
    //
    public static function createObjects()
    {
        $objects = MtsObjectsTvk::find()->all();
        $objectGroups = MtsObjectsGroups::find()->asArray()->indexBy('mg_name')->all();
        $counter = 0;
        foreach ($objects as $object) {
            $counter++;
            echo 'Start create object...№ ' . $counter . PHP_EOL;
            echo $object->name . ' ' . $object->kladr . PHP_EOL;
            $address = $object->kladr;
            $name = $object->name;
            $filial = $object->filial;
            $filialData = ArrayHelper::getValue($objectGroups, $filial);
            $idGroup = ArrayHelper::getValue($filialData, 'mg_id_mts');

            $res = self::getCoordinates($address);

            if (!empty($res->response->GeoObjectCollection->featureMember[0])) {
                $feature = $res->response->GeoObjectCollection->featureMember[0];
                $values = $feature->GeoObject->Point->pos;
                $validAddress = $feature->GeoObject->metaDataProperty->
                GeocoderMetaData->Address->formatted;

                $coords = explode(' ', $values);
                list($latitude, $longitude) = $coords;

            } else {
                $latitude = 52.034206;
                $longitude = 113.472986;
                $validAddress = 'Фэйковый адрес, который нужно заменить';
                echo '****************ошибочный запрос координат********************' . PHP_EOL;
            }

            $response = self::createObjectMts($longitude, $latitude, $name, $validAddress, $idGroup);
            $id = $response->result;
            self::createObjectInDb($object, $id, $longitude, $latitude, $validAddress, $idGroup, $filial, $counter);
        }
        echo 'update time-zones...';
        //update timezones for TimeMode
        Geonames::setTimeZones();
    }

    private static function createObjectInDb($object, $id = 000000000, $longitude, $latitude, $address, $idGroup = 1035048, $filial, $counter)
    {
        /** @var Connection $db */
        $db = \Yii::$app->get('db3');
        $transaction = $db->beginTransaction();

        try {
            $tm = new TimeMode();
            $tm->id_in_mts = $id;
            $tm->object_name = $object->name;

            $tm->ptime_start = '09:00:00';
            $tm->ptime_end = '19:00:00';
            $tm->tm_ptime_start_tue = '09:00:00';
            $tm->tm_ptime_end_tue = '19:00:00';
            $tm->tm_ptime_start_wed = '09:00:00';
            $tm->tm_ptime_end_wed = '19:00:00';
            $tm->tm_ptime_start_thu = '09:00:00';
            $tm->tm_ptime_end_thu = '19:00:00';
            $tm->tm_ptime_start_fri = '09:00:00';
            $tm->tm_ptime_end_fri = '19:00:00';
            $tm->tm_ptime_start_sat = '09:00:00';
            $tm->tm_ptime_end_sat = '19:00:00';
            $tm->tm_ptime_start_sun = '09:00:00';
            $tm->tm_ptime_end_sun = '19:00:00';
            $tm->id_objects = $id;
            $objects = new Objects();
            $objects->id_1c = $object->id_1c;
            $objects->name = $object->name;
            $objects->address = $address;
            $objects->longitude = $longitude;
            $objects->latitude = $latitude;
            $objects->id_in_mts = (integer)$id;
            $objects->group_name = $filial;
            $objects->group_id = $idGroup;
            if ($objects->save()) {
                echo 'объект номер' . $counter . 'создан в базе.................................' . PHP_EOL;
                $tm->save();
            } else {
                $objects->save(false);
                $tm->save();
                echo 'объект id' . $object->id_1c . '---' . $object->name . '  ' . 'ошибка создания объекта++++++++++++++++++++++++++++++++++++++++++++++' . PHP_EOL;
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }
    //
    //create object in mts
    //
    private static function createObjectMts($longitude, $latitude, $name, $address, $idGroup = 1035048)
    {
        $idCreatedObject = new SoapModel(SoapModel::BTB, 'callSaveMapObject',
            ['id' => 11111111, 'radius' => 1000,
                'longitude' => $longitude,
                'latitude' => $latitude, 'imageIndex' => 3,
                'groupId' => $idGroup, 'name' => $name,
                'address' => $address], 'test');
        if ($idCreatedObject->result == 11111111 || empty($idCreatedObject->result)) {
            echo 'mts object create error' . PHP_EOL;
        } else {
            echo 'mts object create' . PHP_EOL;
        }
        return $idCreatedObject;
    }
    //
    //update object in mts
    //
    private static function updateObjectMts($longitude, $latitude, $name, $address, $id = 11111111, $idGroup = 1035048)
    {
        $idUpdatedObject = new SoapModel(SoapModel::BTB, 'callSaveMapObject',
            ['id' => $id, 'radius' => 1000,
                'longitude' => $longitude,
                'latitude' => $latitude, 'imageIndex' => 3,
                'groupId' => $idGroup, 'name' => $name,
                'address' => $address], 'test');
        if ($idUpdatedObject) {
            echo 'mts object updated' . PHP_EOL;
        }
        return $idUpdatedObject;
    }

    private static function addObject($object, $objectGroups, $address, $name, $filial)
    {

        $filialData = ArrayHelper::getValue($objectGroups, $filial);
        $idGroup = ArrayHelper::getValue($filialData, 'mg_id_mts');

        $res = self::getCoordinates($address);

        if (!empty($res->response->GeoObjectCollection->featureMember[0])) {
            $feature = $res->response->GeoObjectCollection->featureMember[0];
            $values = $feature->GeoObject->Point->pos;
            $validAddress = $feature->GeoObject->metaDataProperty->
            GeocoderMetaData->Address->formatted;

            $coords = explode(' ', $values);
            list($longitude, $latitude) = $coords;

        } else {
            $latitude = 52.034206;
            $longitude = 113.472986;
            $validAddress = 'Фэйковый адрес, который нужно заменить';
            echo '****************ошибочный запрос координат********************' . PHP_EOL;
        }

        $response = self::createObjectMts($latitude, $longitude, $name, $validAddress, $idGroup);

        $id = $response->result;
        if (empty($id) || $id == 11111111) {
            self::$messages[$name] = 'ошибка при создании объекта через API';
        } else {
            self::create($object, $id, $longitude, $latitude, $address, $idGroup);
            Geonames::setTimeZone($id);
            self::$messages[$name] = 'Создан новый объект, необходимо проверить адрес и геолокацию';
        }
    }

    private static function create($object, $id = 000000000, $longitude, $latitude, $address, $idGroup = 1035048)
    {
        /** @var Connection $db */
        $db = \Yii::$app->get('db3');
        $transaction = $db->beginTransaction();

        try {
            $tm = new TimeMode();
            $tm->id_in_mts = $id;
            $tm->object_name = $object->Name;

            $tm->ptime_start = '0:00:00';
            $tm->ptime_end = '0:00:00';
            $tm->tm_ptime_start_tue = '0:00:00';
            $tm->tm_ptime_end_tue = '0:00:00';
            $tm->tm_ptime_start_wed = '0:00:00';
            $tm->tm_ptime_end_wed = '0:00:00';
            $tm->tm_ptime_start_thu = '0:00:00';
            $tm->tm_ptime_end_thu = '0:00:00';
            $tm->tm_ptime_start_fri = '0:00:00';
            $tm->tm_ptime_end_fri = '0:00:00';
            $tm->tm_ptime_start_sat = '0:00:00';
            $tm->tm_ptime_end_sat = '0:00:00';
            $tm->tm_ptime_start_sun = '0:00:00';
            $tm->tm_ptime_end_sun = '0:00:00';
            $tm->id_objects = $id;

            $objects = new Objects();
            $objects->id_1c = $object->id_1С;
            $objects->name = $object->Name;
            $objects->address = $address;
            $objects->longitude = $longitude;
            $objects->latitude = $latitude;
            $objects->id_in_mts = $id;
            $objects->group_name = $object->Filial;
            $objects->group_id = $idGroup;

            if ($objects->save(false)) {
                $tm->save();
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }

    private static function notify($messages)
    {
        if (!empty($messages)) {
            \Yii::$app->mailer->compose('layouts/objects', ['messages' => $messages])
                ->setTo('usikov@skl-co.ru')
                ->setFrom(['no-reply@skl-co.ru' => 'SKL Group notifier'])
                ->setSubject('Автоматическое оповещение SKL мобильный сотрудник')
                ->send();
        }
        foreach ($messages as $name=>$message){
            echo $name . $message;
        }
    }
}