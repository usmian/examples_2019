<?php

/**
 * This is the model class for table "user_salary".
 *
 * The followings are the available columns in table 'user_salary':
 * @property integer $user_salary_id
 * @property integer $user_id
 * @property double $value
 * @property string $date_created
 * @property string $date_updated
 * @property integer $project_id
 */
class UserSalary extends PModel
{

    public $saveActivity = false;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{user_salary}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, project_id', 'numerical', 'integerOnly' => true),
            array('value', 'numerical'),
            array('date_created, date_updated', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('user_salary_id, user_id, value, date_created, date_updated, project_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function labels()
    {
        return array(
            'user_salary_id' => 'ID',
            'user_id' => 'Сотрудник',
            'value' => 'Оклад'
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

        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('value', $this->value);

        $criteria = UserController::model()->getClinicsCriteria($criteria, 'user_salary');

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return UserSalary the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param $id
     * @return UserSalary|CActiveRecord
     */
    public function getByUserIdWithCheckIsNew($id)
    {
        $model = self::model()->find('user_id = :id', [':id' => $id]);
        return $model ? $model : new self();
    }

    /**
     * @param $id
     * @return UserSalary|CActiveRecord
     */
    public function getByUserId($id)
    {
        return self::model()->find('user_id = :id', [':id' => $id]);
    }

    /**
     * @param $id
     */
    public function initUserID($id)
    {
        $this->user_id = $id;
    }

    /**
     *
     */
    public function beforeValidate()
    {
        $this->value = Helper::clearPrice($this->value);
        return parent::beforeValidate();
    }

    /**
     * @return array
     */
    public static function getAllValues()
    {
        $models = self::model()->findAll();
        $values = [];
        foreach ($models as $model) {
            $values[$model->user_id] = $model->value;
        }
        return $values;
    }

    /**
     * @return array|mixed|null|string
     */
    public function getUserName()
    {
       return UserController::model()->getNameById($this->user_id);
    }

    /**
     * @return mixed|null
     */
    public function getUserRoles()
    {
        $user = UserController::model()->getById($this->user_id);
        return $user->roleTitles;
    }

    /**
     * @return mixed|null
     */
    public function getUserProfessions()
    {
        $user = UserController::model()->getById($this->user_id);
        return $user->professionTitles;

    }
}
