<?php

/**
 * This is the model class for table "salary_report_service".
 *
 * The followings are the available columns in table 'salary_report_service':
 * @property integer $salary_report_service_id
 * @property integer $salary_report_id
 * @property integer $user_salary_service_id
 * @property integer $service_id
 * @property double $value
 * @property double $original_value
 * @property integer $project_id
 * @property string $date_created
 * @property integer $value_type
 * @property integer $invoice_service_id
 * @property integer $result_value
 * @property integer $count
 * @property integer $type
 */
class SalaryReportService extends PModel
{
    const TYPE_PAY = 1;
    const TYPE_RECOMMENDATION = 2;

    public static $typeNames = [self::TYPE_PAY => 'Оказанная услуга',
        self::TYPE_RECOMMENDATION => 'Рекомендация'];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{salary_report_service}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('salary_report_id, user_salary_service_id, service_id, project_id, count', 'numerical', 'integerOnly' => true),
            array('value, original_value, value_type, result_value, type', 'numerical'),
            array('date_created', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('salary_report_service_id, salary_report_id, user_salary_service_id, service_id, value, original_value, project_id, date_created, value_type, result_value, count, type', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'service' => array(self::BELONGS_TO, 'Service', 'service_id'),
            'invoiceService' => array(self::BELONGS_TO, 'InvoiceService', 'invoice_service_id'),
            'userSalaryService' => array(self::BELONGS_TO, 'UserSalaryService', 'user_salary_service_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function labels()
    {
        return array(
            'salary_report_service_id' => 'Salary Report Service',
            'salary_report_id' => 'Salary Report',
            'user_salary_service_id' => 'UserController Salary Service',
            'service_id' => 'Service',
            'value' => 'Value',
            'original_value' => 'Original Value',
            'project_id' => 'Project',
            'date_created' => 'Date Created',
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
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('salary_report_service_id', $this->salary_report_service_id);
        $criteria->compare('salary_report_id', $this->salary_report_id);
        $criteria->compare('user_salary_service_id', $this->user_salary_service_id);
        $criteria->compare('service_id', $this->service_id);
        $criteria->compare('value', $this->value);
        $criteria->compare('original_value', $this->original_value);
        $criteria->compare('project_id', $this->project_id);
        $criteria->compare('date_created', $this->date_created, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getTypeName()
    {
        return Helper::getFromMap(self::$typeNames, $this->type);
    }

    /**
     * @param $id
     * @param bool $createExcel
     * @return array
     */
    public function getServicesByReportId($id, $createExcel = false)
    {

        $cr = new CDbCriteria();
        $cr->with = [
            'service',
            'invoiceService',
            'invoiceService.patient'
        ];
        $cr->together = true;
        $cr->order = 'invoice_services.date_created ASC';
        $cr->compare('salary_report_service.salary_report_id', $id);
        $models = self::model()->findAll($cr);
        if (empty($models)) {
            return [];
        }
        $services = SalaryReport::prepareServices($models, $createExcel);
        return $services;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SalaryReportService the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param $value
     * @param $type
     * @param $currentValue
     * @return string
     */
    public static function prepareResultValue($value, $type, $currentValue)
    {
        return ($type == 1)
            ? Helper::price($value).' ('. $currentValue . '%)'
            : Helper::price($currentValue);
    }

    /**
     * @param $value
     * @param $type
     * @param $currentValue
     * @param $original
     * @return array
     */
    public static function prepareExcelValue($value, $type, $currentValue, $original)
    {
        return ($type == 1)
            ? ['percent' => $currentValue,
                'value' =>$value]
            : ['percent' => ($currentValue * 100) / $original,
                'value' =>$currentValue];
    }

    /**
     * @param $model
     */
    public static function addCommissions($model)
    {
        self::addCommission($model, 'UserController');
        self::addCommission($model, 'recommendation');
    }

    /**
     * @param $model
     * @param $switch
     * @return mixed
     */
    private static function addCommission($model, $switch)
    {
        /** @var SalaryReport $model */
        $value = ($switch == 'UserController') ? 'value' : 'recommendation_value';
        $type = ($switch == 'UserController') ? self::TYPE_PAY : self::TYPE_RECOMMENDATION;
        $invoice = ("SELECT rpt_service.invoice_service_id 
                       FROM salary_report_service rpt_service 
                       INNER JOIN invoice_services inv_service on inv_service.invoice_service_id = rpt_service.invoice_service_id
                       WHERE rpt_service.type={$type}");

        $in = "WHERE inv_srv.{$switch}_id = :user_id and usr_sal.user_id = :user_id and inv.status = 2 and usr_sal.{$value} IS NOT NULL and 
         rpt.invoice_service_id IS NULL and inv_srv.date_created >= :date_from and inv_srv.date_created <= :date_to";

        $sql = "INSERT INTO salary_report_service(salary_report_id, user_salary_service_id, service_id, value, 
                 original_value, project_id, date_created, invoice_service_id, value_type, result_value, count, type)
                 SELECT :salary_report_id, usr_sal.user_salary_service_id, inv_srv.service_id,  usr_sal.{$value}, 
                 inv_srv.value, :project_id, inv.date_created, inv_srv.invoice_service_id,  usr_sal.value_type, 
                 (CASE WHEN usr_sal.value_type=1 THEN ((inv_srv.full_value/100)*usr_sal.value)*inv_srv.count ELSE usr_sal.value*inv_srv.count END) as result, 
                 inv_srv.count, :type
                    FROM invoice_services inv_srv 
                    LEFT JOIN invoices inv on inv_srv.invoice_id = inv.invoice_id
                    LEFT JOIN ({$invoice}) as rpt on inv_srv.invoice_service_id=rpt.invoice_service_id
                    LEFT JOIN user_salary_service usr_sal on inv_srv.service_id = usr_sal.service_id
                {$in}";

        return Yii::app()->db->createCommand($sql)->queryAll(
            true,
            array(':salary_report_id' => $model->salary_report_id, ':user_id' => $model->user_id, ':date_from' => $model->date_from,
                ':date_to' => $model->date_to, ':project_id' => $model->project_id, ':type' => $type)
        );
    }
}
