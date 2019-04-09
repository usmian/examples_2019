<?php

/**
 * This is the model class for table "user_salary_service".
 *
 * The followings are the available columns in table 'user_salary_service':
 * @property integer $user_salary_service_id
 * @property integer $user_id
 * @property integer $service_id
 * @property integer $value_type
 * @property double $value
 * @property integer $recommendation_value_type
 * @property double $recommendation_value
 * @property string $date_created
 * @property integer $project_id
 */
class UserSalaryService extends PModel
{
    const DISCOUNT_TYPE_RUB = 2;
    const DISCOUNT_TYPE_PERCENT = 1;

    public static $discountTypes = array(
        self::DISCOUNT_TYPE_RUB => 'руб',
        self::DISCOUNT_TYPE_PERCENT => '%'
    );

    public $service_name;
    public $service_code;
    public $service_category;

    /**
     * @return string the associated database table name
     */

    public function tableName()
    {
        return '{{user_salary_service}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, service_id, value_type, recommendation_value_type, project_id', 'numerical', 'integerOnly' => true),
            array('value, recommendation_value', 'numerical'),
            array('date_created', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('user_salary_service_id, user_id, service_id, value_type, value, recommendation_value, recommendation_value_type date_created, project_id, service_name, service_code, service_category', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'service' => array(self::BELONGS_TO, 'Service', 'service_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function labels()
    {
        return array(
            'user_salary_service_id' => 'ID',
            'user_id' => 'Сотрудник',
            'service_id' => 'Услуга',
            'code' => 'Код услуги',
            'price' => 'Цена услуги',
            'value' => 'Оказанная услуга',
            'recommendation_value' => 'Рекомендация',
            'service_name' => 'Услуга',
            'service_code' => 'Артикул',
            'service_category' => 'Категория',

        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('user_salary_service_id', $this->user_salary_service_id);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('service_id', $this->service_id);
        $criteria->compare('value_type', $this->value_type);
        $criteria->compare('value', $this->value);
        $criteria->compare('date_created', $this->date_created);
        $criteria->compare('project_id', $this->project_id);

        $criteria = $this->limitParams($criteria);

        if (!isset($_GET['UserSalaryService_sort'])) {
            $criteria->order = 'user_salary_service.value ASC';
        }

        return new CActiveDataProvider($this, array(
            'pagination' => [
                'pageSize' => 20,
            ],
            'criteria' => $criteria,

        ));
    }

    /**
     * @param $criteria
     * @return mixed
     */
    private function limitParams($criteria)
    {
        $criteria->with = array('service',
            'service.category');
        $criteria->together = true;

        $criteria->compare('UPPER(services.name)', Helper::toUpper($this->service_name), true);
        $criteria->compare('UPPER(services.code)', Helper::toUpper($this->service_code), true);
        $criteria->compare('category.service_category_id', $this->service_category);
        return $criteria;
    }

    /**
     * @param $id
     */
    public function initUserID($id)
    {
        $this->user_id = $id;
    }

    /**
     * @param $value
     * @param $type
     * @param $recommend
     */
    public function updateByValues($value, $type, $recommend)
    {
        if ($recommend) {
            $this->recommendation_value = $value;
            $this->recommendation_value_type = $type;
        } else {
            $this->value = $value;
            $this->value_type = $type;
        }
        $this->save();

        UserSalary::model()->clearCache();
        UserSalaryService::model()->clearCache();
        ServiceCategory::model()->clearCache();
    }


    /**
     * @param $value
     * @param $type
     * @param $userId
     * @param $serviceId
     * @param $recommend
     */
    public function updateByValuesNew($value, $type, $userId, $serviceId, $recommend)
    {
        if (!$this->exists("user_id=$userId AND service_id=$serviceId")) {
            $this->user_id = $userId;
            $this->service_id = $serviceId;
            $this->updateByValues($value, $type, $recommend);
        }
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return UserSalaryService the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param $id
     * @return CActiveRecord[]
     */
    public function getServicesByUserId($id)
    {
        return self::model()
            ->findAllByAttributes(['user_id' => $id],
                ['order' => 'value DESC']);
    }

    /**
     * @return bool
     */
    public function afterFind()
    {
        return parent::afterFind();
    }

    /**
     * @return bool
     */
    public function beforeSave()
    {
        $this->value = Helper::clearPrice($this->value);
        $this->value_type = (!empty($this->value_type)) ? $this->value_type : self::DISCOUNT_TYPE_PERCENT;
        return parent::beforeSave();
    }
}
