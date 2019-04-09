<?php

class ApiController extends AController
{

    /**
     * @return array
     */
    protected function actionsMap()
    {
        return array(
            'list' => [
                'comment',
                'getDetailing',
                'getReportExcel'
            ],
            'manage' => [
                'getServices',
                'getCosts',
                'updateService',
                'deleteService',
                'deleteCost',
                'pay',
                'updateAllServices',
                'getServicesReport',
                'getHoursReport',
            ]
        );
    }

    /**
     * @param $id
     */
    public function actionGetReportExcel($id)
    {
        SalaryReport::createReportExcel($id);
    }

    /**
     * @param $id
     */
    public function actionGetServices($id)
    {
        $data = SalaryReportService::model()->getServicesByReportId($id);
        $this->response($data);
    }

    /**
     * @param $id
     */
    public function actionGetCosts($id)
    {
        $data = Cost::model()->getCostsByReportId($id);
        $this->response($data);
    }

    /**
     * @param $id
     */
    public function actionDetail($id)
    {
        $data = SalaryReport::model()->getDetailsById($id);
        $this->response($data);
    }

    /**
     * @param $id
     */
    public function actionGetDetailing($id)
    {
        $model = SalaryReport::model()->getById($id);
        if ($model->status !== SalaryReport::STATUS_CLOSED) {
            $model->updateCommissions();
        }
        $result = SalaryReport::model()->getDetailing($id);
        $this->response($result);
    }

    /**
     *
     */
    public function actionDeleteService()
    {
        $post = Yii::app()->request->getPost('data');
        $reportServiceId = $post['id'];
        $reportId = $post['report_id'];

        SalaryReportService::model()->deleteById($reportServiceId);
        $model = SalaryReport::model()->findByPk($reportId);
        $model->save();
        $value = $model->services_value;
        $this->response(['report_id' => $reportId, 'value' => $value]);
    }

    /**
     *
     */
    public function actionDeleteCost()
    {
        $post = Yii::app()->request->getPost('data');
        $costId = $post['id'];
        $reportId = $post['report_id'];
        $cost = Cost::model()->findByPk($costId);
        $previousValue = $cost->value;
        $cost->delete();
        $model = SalaryReport::model()->findByPk($reportId);
        $value = $model->recalculatePaidValue($previousValue);
        $this->response(['report_id' => $reportId, 'value' => $value]);
    }

    /**
     *
     */
    public function actionUpdateService()
    {
        $userServiceId = Yii::app()->request->getPost('user_salary_id');
        $value = Yii::app()->request->getPost('value');
        $type = Yii::app()->request->getPost('type');
        $userId = Yii::app()->request->getPost('user_id');
        $serviceId = Yii::app()->request->getPost('service_id');
        $recServiceId = Yii::app()->request->getPost('rs_id');

        //
        $model = $userServiceId
            ? UserSalaryService::model()->findByPk($userServiceId)
            : new UserSalaryService();

        if (!$model || !$userId) {
            $this->error(500);
        }
        $recommend = (!$recServiceId) ? false : true;
        if ($model->isNewRecord) {
            $model->updateByValuesNew($value, $type, $userId, ($serviceId) ? $serviceId : $recServiceId, $recommend);
        } else {
            $model->updateByValues($value, $type, $recommend);
        }

        //
        $this->response(true);
    }

    /**
     *
     */
    public function actionUpdateAllServices()
    {
        $value = Yii::app()->request->getPost('value');
        $services = new Service('search');
        $services->name = Yii::app()->request->getPost('service');
        $services->code = Yii::app()->request->getPost('code');
        $services->service_category_id = Yii::app()->request->getPost('categoryId');
        $services->profession_id = Yii::app()->request->getPost('professionId');
        $type = Yii::app()->request->getPost('type');
        $userId = Yii::app()->request->getPost('userId');
        $recommend = Yii::app()->request->getPost('recommend');
        $filtered = $services->searchAll($userId);
        $services->updateLimited($filtered, $value, $type, $userId, $recommend);

        $this->response('ok', 0);
    }

    /**
     *
     */
    public function actionPay()
    {
        $id = Yii::app()->request->getPost('id');
        $sum = Yii::app()->request->getPost('sum');
        $paid = Yii::app()->request->getPost('paid');
        $type = Yii::app()->request->getPost('type');
        $date = Yii::app()->request->getPost('date');

        $r = SalaryReport::model()->pay($id, $sum, $paid, $type, $date);
        $this->response($r);
    }

    /**
     * @param null $draw
     */
    public function actionGetServicesReport($draw = null)
    {
        $params = $_GET;
        //
        if (Helper::getFromMap($params, 'download')) {
            $csv = InvoiceService::model()->getSalaryReportCsv($params);
            $name = 'Зарплаты - выгрузка по услугам от ' . Yii::app()->clock->date('d.m.Y H:i') . '.csv';
            header('Content-Type: text/csv');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . $name . "\"");
            echo $csv;
            return;
        }


        //
        $data = InvoiceService::model()->getSalaryReport($params);
        $count = InvoiceService::model()->getSalaryReport($params, true);
        $results = [
            'draw' => (int)$draw,
            "iTotalRecords" => $count,
            "iTotalDisplayRecords" => $count,
            "aaData" => $data
        ];
        $this->response($results, 0, true);
    }

    /**
     *
     */
    public function actionGetHoursReport()
    {
        $params = $_GET;

        //
        $users = User::model()->getSalaryHoursReport($params);
        $params['users'] = array_keys($users);
        $type = Helper::getFromMap($params, 'type');

        $data = $users;
        if (!$type || $type == 1) {
            $data = Schedule::model()->getHoursReport($params, $users);
        }
        if (!$type || $type == 2) {
            $data = Appointment::model()->getHoursReport($params, $data);
        }
        $result = [];
        foreach ($data as $u => $items) {
            $items['id'] = $u;
            if ($items['total']['appointments'] && $items['total']['schedule']) {
                $items['total']['ratio'] = round($items['total']['appointments'] / $items['total']['schedule'], 2);
            }
            $result[] = $items;
        }

        //
        if (Helper::getFromMap($params, 'download')) {
            $csv = User::model()->prepareSalaryHoursCsv($result, $type);
            $name = 'Зарплаты - выгрузка по часам ' . ($type ? ($type == 1 ? 'по расписанию' : 'по визитам') : '') . ' от ' . Yii::app()->clock->date('d.m.Y H:i') . '.csv';
            header('Content-Type: text/csv');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . $name . "\"");
            echo $csv;
            return;
        }

        $this->response($result);
    }
}