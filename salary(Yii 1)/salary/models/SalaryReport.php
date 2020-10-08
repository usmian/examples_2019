<?php

/**
 * This is the model class for table "salary_report".
 *
 * The followings are the available columns in table 'salary_report':
 * @property integer $salary_report_id
 * @property integer $user_id
 * @property string $date_from
 * @property string $date_to
 * @property string $date_created
 * @property string $date_updated
 * @property integer $reporter_id
 * @property double $value
 * @property double $salary_value
 * @property double $services_value
 * @property double $bonus_value
 * @property string $bonus_comment
 * @property double $penalty_value
 * @property string $penalty_comment
 * @property integer $project_id
 * @property boolean $in_trash
 * @property integer $status
 * @property string $comment
 * @property double $paid_value
 */
class SalaryReport extends PModel
{

    const STATUS_NEW = 1;
    const STATUS_CLOSED = 2;

    public $period;

    public $user_name;
    public $custom_dates;

    public $_date_inv;
    public $_patient_name;

    public $_service_name;
    public $_service_code;
    public $_service_count;
    public $_percent;
    public $_value;

    public $_value_type;
    public $_original_value;

    public $saveActivity = false;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{salary_report}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id', 'required'),
            array('user_id, reporter_id, project_id, status', 'numerical', 'integerOnly' => true),
            array('period', 'required', 'on' => 'create'),
            array('custom_dates', 'required', 'on' => 'custom', 'message' => 'Необходимо выбрать период'),
            array('salary_value, services_value, penalty_value, bonus_value, paid_value', 'numerical'),
            array('date_from, date_to, date_created, date_updated, bonus_comment, penalty_comment, in_trash, period, user_name, comment, custom_dates', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('salary_report_id, user_id, date_from, date_to, date_created, date_updated, reporter_id, value, salary_value, services_value, bonus_comment, penalty_value, penalty_comment, project_id, in_trash, status, period, user_name, bonus_value, comment, custom_dates, paid_value', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'UserController' => array(self::BELONGS_TO, 'UserController', 'user_id'),
            'salaryReportService' => array(self::HAS_MANY, 'SalaryReportService', 'salary_report_id'),
            'cost' => [self::HAS_MANY, 'Cost', 'salary_report_id'],
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function labels()
    {
        return array(
            'user_id' => 'Сотрудник',
            'date_from' => 'Период',
            'date_created' => 'Дата расчета',
            'reporter_id' => 'Автор',
            'value' => 'Общая сумма',
            'salary_value' => 'Оклад',
            'bonus_comment' => 'Комментарий',
            'penalty_value' => 'Штраф',
            'penalty_comment' => 'Комментарий',
            'status' => 'Статус',
            'bonus_value' => 'Бонус',
            'period' => 'Период',
            'custom_dates' => 'Выбрать период',
            'paid_value' => 'Выплачено'
        );
    }

    /**
     *
     */
    public function afterSave()
    {
        if ($this->isNewRecord && $this->comment) {
            Comment::create('SalaryReport', $this->salary_report_id, $this->comment, $this->reporter_id);
        }
        return parent::afterSave();
    }

    /**
     * @param bool $inTrash
     * @param null $user
     * @return CActiveDataProvider
     */
    public function search($inTrash = false, $user = null)
    {
        $criteria = new CDbCriteria;

        if (!isset($_GET['SalaryReport_sort'])) {
            $criteria->order = 'salary_report.date_from DESC, salary_report.date_created DESC';
        }

        $criteria->addCondition('salary_report.in_trash is ' . ($inTrash ? 'TRUE' : 'NOT TRUE'));
        $criteria->compare('salary_report.user_id', $user ? $user : $this->user_id);
        $criteria->compare('salary_report.status', $this->status);

        $criteria = UserController::model()->getClinicsCriteria($criteria, 'salary_report');

        $criteria->with = array(['UserController', 'resetScope' => true]);
        $criteria->together = true;


        if ($this->date_from) {
            $criteria = Helper::extendCriteriaWithDateRange($criteria, $this, 'date_from', 'date_from');
        }

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SalaryReport the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * /**
     * @param bool $full
     * @return array
     */
    public static function getStatusList()
    {
        return [self::STATUS_NEW => 'новый', self::STATUS_CLOSED => 'закрыт'];
    }

    /**
     * @param $models
     * @return array
     */
    public static function prepareServices($models, $createExcel = false)
    {
        $res = [];

        /** @var SalaryReportService $service */
        foreach ($models as $service) {
            $item['date_service'] = !$createExcel ? (new DateTime($service->invoiceService->date_created))->format('d.m.Y') : $service->invoiceService->date_created;
            $item['patient_name'] = Helper::getShortName($service->invoiceService->patient->full_name);
            $item['original_value'] = !$createExcel ? Helper::price($service->original_value) : $service->original_value;
            $item['count'] = $service->count;
            $item['service_name'] = $service->service->name;
            $item['code'] = $service->service->code;
            $item['salary_report_id'] = $service->salary_report_id;
            $item['salary_report_service_id'] = $service->salary_report_service_id;
            $item['result_value'] = !$createExcel
                ? SalaryReportService::prepareResultValue($service->result_value, $service->value_type, $service->value)
                : SalaryReportService::prepareExcelValue($service->result_value, $service->value_type, $service->value, $service->original_value);
            $item['type_name'] = $service->getTypeName();
            $item['type'] = $service->type;
            $res[] = $item;
        }
        return $res;
    }

    /**
     * @return bool|void
     */
    public function beforeValidate()
    {
        $this->salary_value = Helper::clearPrice($this->salary_value);
        $this->bonus_value = Helper::clearPrice($this->bonus_value);
        $this->penalty_value = Helper::clearPrice($this->penalty_value);
        if ($this->period == 'custom') {
            $this->setScenario('custom');
        }
        return parent::beforeValidate();
    }

    /**
     * @return bool
     */
    public function beforeSave()
    {
        if (!$this->reporter_id) {
            $this->reporter_id = Yii::app()->user->id;
        }
        if ($this->period == 'custom') {
            $dates = explode('-', $this->custom_dates);
            $this->date_from = Helper::date(trim($dates[0]), 'Y-m-d 00:00:00');
            $this->date_to = Helper::date(trim($dates[1]), 'Y-m-d 23:59:00');
        } else {
            $dates = explode('R', $this->period);
            $this->date_from = $dates[0];
            $this->date_to = $dates[1];
        }
        if (!$this->status) {
            $this->status = self::STATUS_NEW;
        }
        $this->calculateValue();
        return parent::beforeSave();
    }

    /**
     *
     */
    private function calculateValue()
    {
        if (!($this->isNewRecord)) {
            $this->recalculateValue($this->salary_report_id);
        }
        $this->value = $this->salary_value + $this->bonus_value - $this->penalty_value + (!empty($this->services_value) ? $this->services_value : 0);
    }

    /**
     * @param $salaryReportId
     */
    public function recalculateValue($salaryReportId)
    {
        $services = SalaryReportService::model()->findAllByAttributes(['salary_report_id' => $salaryReportId]);
        $resultValue = 0;
        /** @var SalaryReportService $service */
        foreach ($services as $service) {
            $resultValue += $service->result_value;
        }
        $this->services_value = $resultValue;
    }

    /**
     * @return bool|void
     */
    public function afterFind()
    {
        if (!($this->isNewRecord)) {
            $this->period = $this->date_from . 'R' . $this->date_to;
        }
    }

    /**
     * @return string
     */
    public function getDatesTitle()
    {
        return Helper::getBeautyDate($this->date_from, false, true) . ' - '
            . Helper::getBeautyDate($this->date_to, false, true);
    }


    /**
     * @param $status
     * @return null
     */
    public function getStatusTitle($status = '')
    {
        return Helper::getFromMap(self::getStatusList(true), $status);
    }

    /**
     * @return array|mixed|null|string
     */
    public function getName()
    {
        return UserController::model()->getNameById($this->user_id);
    }

    /**
     * @return int|string
     */
    public function getDateCreated()
    {
        return Helper::getBeautyDate($this->date_created, false, true);
    }

    /**
     * @return int|string
     */
    public function getDateUpdated()
    {
        return Helper::getBeautyDate($this->date_updated, false, true);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return Helper::price($this->value);
    }

    /**
     * @return string
     */
    public function getPaidValue()
    {
        return Helper::price($this->paid_value);
    }

    /**
     * @return array
     */
    public static function getDatesList()
    {
        $now = Yii::app()->clock->time();
        return [
            Helper::date($now, 'Y-m-01 00:00:00') . ' R ' . Helper::date($now, 'Y-m-t 23:59:59') => 'Текущий месяц',
            Helper::date('-1 month', 'Y-m-01 00:00:00') . ' R ' . Helper::date('-1 month', 'Y-m-t 23:59:59') => 'Прошлый месяц',
            'custom' => 'Выбрать период'
        ];
    }

    /**
     * @param $reportId
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function createReportExcel($reportId)
    {
        /** @var SalaryReport $report */
        $report = SalaryReport::model()->getById($reportId);
        $services = SalaryReportService::model()->getServicesByReportId($reportId, true);
        //
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 'Сотрудник:');
        $username = UserController::model()->getNameById($report->user_id);
        $sheet->setCellValue('B2', $username);
        $sheet->setCellValue('A3', 'Период:');
        $sheet->setCellValue('B3', $report->getDatesTitle());

        $sheet->setCellValue('A4', 'Оклад:');
        $sheet->setCellValue('B4', $report->salary_value);
        $sheet->getStyle("B4")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->setCellValue('A5', '');
        $arow = 6;
        $style = $headStyle = $footerStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => '000000']]
            ]
        ];
        $headStyle['alignment'] = [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ];
        $headStyle['font'] = $footerStyle['font'] = [
            'bold' => true,
        ];
        if (!empty($services)) {
            $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
            foreach ($columns as $c) {
                $sheet->getColumnDimension($c)->setAutoSize(true);
            }
            //
            $sheet->setCellValue('A7', '№ п/п');
            $sheet->setCellValue('B7', 'Дата');
            $sheet->setCellValue('C7', 'Пациент');
            $sheet->setCellValue('D7', 'Код услуги');
            $sheet->setCellValue('E7', 'Наименование услуги');
            $sheet->setCellValue('F7', 'Кол-во');
            $sheet->setCellValue('G7', 'Стоимость');
            $sheet->setCellValue('H7', 'Комиссия, руб.');
            $sheet->setCellValue('I7', 'Комиссия, %');
            $sheet->setCellValue('J7', 'Тип');
            $sheet->getStyle('A7:J7')->applyFromArray($headStyle);
            //
            $n = 1;
            $row = 8;
            $sum = 0;
            $comission = 0;
            $count = 0;

            foreach ($services as $service) {
                $startRow = $row;
                $sheet->setCellValue("A{$row}", $n);
                $date = new DateTime($service['date_service']);
                $date = $date->format('d.m.Y');
                $sheet->setCellValue("B{$row}", $date);
                $sheet->setCellValue("C{$row}", $service['patient_name']);
                $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue("D{$row}", $service['code']);
                $sheet->setCellValue("E{$row}", $service['service_name']);
                $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue("F{$row}", $service['count']);
                $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue("G{$row}", $service['original_value']);
                $sheet->setCellValue("H{$row}", $service['result_value']['value']);
                $sheet->setCellValue("I{$row}", $service['result_value']['percent']);
                $sheet->setCellValue("J{$row}", $service['type_name']);
                //
                $sheet->getStyle("G{$row}:J{$row}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle("A{$startRow}:J{$row}")->applyFromArray($style);
                $sum += $service['original_value'];
                $comission += $service['result_value']['value'];
                $count += $service['count'];
                $row++;
                $n++;
            }
        }
        if (isset($count)) {
            $arow = $row + 2;
            $sheet->setCellValue("E{$row}", 'Всего: ');
            $sheet->setCellValue("F{$row}", $count);
            $sheet->setCellValue("G{$row}", $sum);
            $sheet->setCellValue("H{$row}", $comission);

            $sheet->getStyle("G{$row}:H{$row}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("E{$row}:J{$row}")->applyFromArray($footerStyle);
        }
        $sheet->setCellValue("A{$arow}", 'Общая сумма: ');
        $sheet->setCellValue("B{$arow}", $report->value);
        $sheet->getStyle("B{$arow}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        //
        $title = "Расчет_{$username}_период_{$report->getDatesTitle()}";
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel; charset=cp1251');
        header('Content-Disposition: attachment; filename="' . $title . '.xlsx"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save("php://output");
    }

    /**
     * @param $id
     * @return array|null
     */
    public function getDetailsById($id)
    {
        /** @var SalaryReport $model */
        $model = self::model()->getById($id);
        if (!$model) {
            return null;
        }
        $date = date('d.m.Y');
        return [
            'dates' => $model->getDatesTitle(),
            'UserController' => $model->getUserName(true),
            'value' => $model->value,
            'costs' => Cost::model()->getCostsByReportId($id),
            'paid' => $model->paid_value,
            'limit' => $model->value - $model->paid_value,
            'date' => $date
        ];
    }

    /**
     * @param bool $full
     * @return mixed
     */
    public function getUserName($full = false)
    {
        return !$full ? Helper::getShortName($this->user->name) :
            '[' . $this->user->roleTitles . '] ' . $this->user->name;
    }

    /**
     * @return mixed
     */
    public function getUserNameForPay()
    {
        return $this->user->name;
    }

    /**
     * @param $reportId
     * @return array|null
     */
    public function getDetailing($reportId)
    {
        $cr = new CDbCriteria();
        $cr->with = [
            'salaryReportService',
            'salaryReportService.service',
            'salaryReportService.invoiceService',
            'salaryReportService.invoiceService.patient'
        ];
        $cr->together = true;
        $cr->compare('salary_report.salary_report_id', $reportId);
        $cr->order = 'invoice_services.date_created ASC';
        /** @var SalaryReport $model */
        $model = self::find($cr);

        if (!$model) {
            return null;
        }

        return [
            'report' => $model->prepareReport(),
            'services' => self::prepareServices($model->salaryReportService),
            'costs' => Cost::model()->getCostsByReportId($reportId),
            'sum' => Helper::price($model->services_value)
        ];
    }

    /**
     * @return array
     */
    private function prepareReport()
    {
        $report = [];
        $report['period'] = Helper::getBeautyDate($this->date_from, true, true) . ' - ' . Helper::getBeautyDate($this->date_to, true, true);
        $report['salary_report_id'] = $this->salary_report_id;
        $report['create_report'] = Helper::getBeautyDateTime($this->date_created);
        $report['update_report'] = Helper::getBeautyDateTime($this->date_updated);
        $report['value'] = Helper::price($this->value);
        $report['salary'] = Helper::price($this->salary_value);
        $report['status'] = SalaryReport::model()->getStatusTitle($this->status);
        $report['user_name'] = UserController::model()->getNameById($this->user_id);
        $report['bonus_value'] = (!empty($this->bonus_value)) ? Helper::price($this->bonus_value) : '';
        $report['bonus_comment'] = $this->bonus_comment;
        $report['penalty_value'] = (!empty($this->penalty_value)) ? Helper::price($this->penalty_value) : '';
        $report['paid_value'] = $this->paid_value ? Helper::price($this->paid_value) : '0.00 руб.';
        $report['penalty_comment'] = $this->penalty_comment;

        return $report;
    }

    /**
     *
     */
    public function updateCommissions()
    {
        $cr = new CDbCriteria();
        $cr->with = [
            'userSalaryService'
        ];
        $cr->together = true;
        $cr->condition = ('salary_report_id= :sid');
        $cr->params = [':sid' => $this->salary_report_id];
        $services = SalaryReportService::model()->with('userSalaryService')->findAll($cr);
        /** @var SalaryReportService $service */
        $result = 0;
        foreach ($services as $service) {
            if ($service->type == SalaryReportService::TYPE_PAY) {
                $value =  $service->userSalaryService->value;
                $type = $service->userSalaryService->value_type;
                $service->value = $value;
                $service->value_type = $type;
                $service->result_value = ($type==1) ? ($service->original_value/100)*$value : $value ;
            } else {
                $value = $service->userSalaryService->recommendation_value;
                $type = $service->userSalaryService->recommendation_value_type;
                $service->value = $value;
                $service->value_type = $type;
                $service->result_value = ($type==1) ? ($service->original_value/100)*$value : $value ;
            }
            $service->save();
            $result += $service->result_value;
        }
        $this->services_value = $result;
        $this->save();
    }

    /**
     * @param $id
     * @param $sum
     * @param $paid
     * @param $type
     * @param $date
     * @return array|bool
     */
    public function pay($id, $sum, $paid, $type, $date)
    {
        /** @var SalaryReport $model */
        $model = self::model()->getById($id);
        $allPaid = $sum + $paid + $model->paid_value;
        $allPaid = Helper::roundDouble($allPaid);
        $value = Helper::roundDouble($model->value);

        if (!$model || $allPaid!=$value) {
            return false;
        }
        $model->payPart($paid);
        //
        $title = 'Сотрудник: ' . $model->getUserNameForPay() . ', период: ' . $model->getDatesTitle();
        //
        $paramsPay = ['value' => $paid, 'type' => $type, 'comment' => $title, 'date' => $date, 'salary_report_id' => $id];
        Cost::model()->addPay($paramsPay, $model);

        //
        // $model->status = self::STATUS_CLOSED;
        return ['save' => $model->save(),
            'paid' => $model->paid_value,
            'modal' => (Helper::roundDouble($model->value) - Helper::roundDouble($model->paid_value)) > 0 ? true : false
        ];
    }

    /**
     *
     */
    public function checkLimit()
    {
        return (Helper::roundDouble($this->value)) - (Helper::roundDouble($this->paid_value)) > 0;
    }

    /**
     * @param $paid
     */
    private function payPart($paid)
    {
        $this->paid_value = empty($this->paid_value) ? $paid : $this->paid_value + $paid;
    }

    public function recalculatePaidValue($previousValue)
    {
        $this->paid_value = $this->paid_value - $previousValue;
        $this->save();
        return $this->paid_value;
    }
}
