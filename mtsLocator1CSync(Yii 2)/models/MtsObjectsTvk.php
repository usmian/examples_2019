<?php

namespace app\modules\mtsgps\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "mts_objects_tvk".
 *
 * @property integer $id
 * @property string $id_1c
 * @property string $name
 * @property string $filial
 * @property string $kladr
 * @property string $phones
 * @property string $schedule
 * @property string $site
 * @property string $email
 * @property string $selloutchannel
 * @property string $created_at
 */
class MtsObjectsTvk extends \yii\db\ActiveRecord
{

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],

            ],
        ];
    }


    public static function tableName()
    {
        return 'mts_objects_tvk';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db3');
    }

    public static function isNeedRename($field, $id, $name)
    {
        return self::find()->where(['id_1c'=> $id])
            ->andWhere(['!=', $field , $name])
            ->one();
    }

    public static function isExists($id){
        return self::find()->where(['id_1c' => $id])->exists();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at',], 'safe'],
            [['id_1c', 'phones', 'filial'], 'string', 'max' => 45],
            [['kladr', 'schedule', 'site', 'email', 'selloutchannel', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_1c' => 'Id 1c',
            'kladr' => 'Kladr',
            'phones' => 'Phones',
            'schedule' => 'Schedule',
            'site' => 'Site',
            'email' => 'Email',
            'selloutchannel' => 'Selloutchannel',
        ];
    }
}
