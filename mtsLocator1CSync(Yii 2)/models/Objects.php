<?php

namespace app\modules\mtsgps\models;

use Yii;

/**
 * This is the model class for table "tmp_objects".
 *
 * @property integer $id_objects
 * @property integer $id_time_mode
 * @property double $longitude
 * @property double $latitude
 * @property integer $id_in_mts
 * @property integer $group_id
 * @property string $group_name
 * @property integer $super_id
 * @property string $super_name
 * @property string $name
 * @property string $address
 * @property integer $id_1c
 *
 * @property TimeMode $idTimeMode
 */
class Objects extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mts_objects';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db3');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['radius', 'id_in_mts', 'group_id', 'super_id', 'id_1c'], 'safe'],
            [['longitude', 'latitude'], 'number'],
            [['group_name', 'super_name', 'address'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 55],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_objects' => 'Id Objects',
            'id_time_mode' => 'Id Time Mode',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'id_in_mts' => 'Id In Mts',
            'group_id' => 'Group ID',
            'group_name' => 'Group Name',
            'super_id' => 'Super ID',
            'super_name' => 'Super Name',
            'name' => 'Name',
            'address' => 'Address',
            'id_1c' => 'Id 1c',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTimeMode()
    {
        return $this->hasOne(TimeMode::className(), ['id_in_mts' => 'id_in_mts']);
    }

    public function getLocations()
    {
        return $this->hasMany(Locations::className(), ['object_ids' => 'id_in_mts']);
    }

 /*   public function getTimeMode()
    {
        return $this->hasOne(TimeMode::className(), ['id_in_mts' => 'id_in_mts']);
    }*/

    public function getMtsObjectsGroups()
    {
        return $this->hasOne(MtsObjectsGroups::className(), ['mg_id_mts' => 'group_id']);
    }

}
