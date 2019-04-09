<?php


class DefaultController extends MController
{

    /**
     * @return array
     */
    protected function actionsMap()
    {
        return array(
            'manage' => [
                'settings',
                'settingsUpdate',
                'servicesUpdate',
                'reportCreate',
                'reportUpdate',
                'reportClose',
                'services',
                'hours'
            ],
        );
    }

    /**
     *
     */
    public function actionReportCreate()
    {
        $this->reportForm('create');
    }


    /**
     * @param $id
     * @throws CHttpException
     */
    public function actionReportUpdate($id)
    {
        $this->reportForm('update', $id);
    }

    /**
     *
     */
    public function actionIndex()
    {
        $this->module->css(array(
            'css/style.css'
        ));
        $model = new SalaryReport('search');
        $model->unsetAttributes();
        $requestData = Yii::app()->request->getParam('SalaryReport');
        if ($requestData && is_array($requestData)) {
            $model->attributes = $requestData;
            $model->user_name = Helper::getFromMap($requestData, 'user_name');
        }
        $this->render('index', array('model' => $model));
    }

    /**
     *
     */
    public function actionSettings()
    {
        $model = $this->getListModel();
        $this->render('settings', array('model' => $model));
    }


    /**
     * @param $id
     * @throws CHttpException
     */
    public function actionSettingsUpdate($id)
    {
        $this->module->css(array(
            'css/style.css'
        ));
        $this->settingsForm($id);
    }


    /**
     * @param $action
     * @param null $id
     * @throws CHttpException
     */
    private function reportForm($action, $id = null)
    {
        $this->module->css(array(
            'css/style.css'
        ));

        $model = ($action === 'update')
            ? SalaryReport::model()->getById($id)
            : new SalaryReport($action);

        if (!$model) {
            throw new CHttpException('404');
        }

        if ($action === 'update' && $model->status != SalaryReport::STATUS_CLOSED){
            $model->updateCommissions();
        }

        if (isset($_POST['SalaryReport'])) {
            $model->attributes = $_POST['SalaryReport'];
            $model->period = Yii::app()->request->getPost('SalaryReport')['period'];
            if ($model->validate()) {
                $model->save();
                $this->setSuccess(($action === 'create') ? 'Расчет успешно добавлен' : 'Расчет сохранен');
                if ($action == 'create') {
                    SalaryReportService::addCommissions($model);
                    $model->save();
                    $this->redirect(Yii::app()->createUrl('/salary/default/reportUpdate/id/' . $model->salary_report_id));
                } else {
                    $this->redirect(Yii::app()->createUrl('/salary/default/index'));
                }
            }
        }

        $this->render('createUpdate', array(
            'model' => $model, 'action' => $action, 'id' => $id
        ));
    }


    /**
     * @param null $id
     * @throws CHttpException
     */
    private function settingsForm($id = null)
    {
        $model = UserSalary::model()->getByUserIdWithCheckIsNew($id);
        $model->initUserId($id);

        if (!$model) {
            throw new CHttpException('404');
        }

        $isNew = $model->isNewRecord;

        if (isset($_POST['UserSalary'])) {
            $model->attributes = Yii::app()->request->getPost('UserSalary');
            if ($model->validate()) {
                $model->save(false);
                if ($isNew) {
                    $this->refresh();
                }else{
                    $this->setSuccess('Настройки зарплаты сохранены');
                    $this->redirect('/salary/default/settings');
                }
            }
        }

        $services = new Service('search');
        $services->unsetAttributes();
        //
        $requestData = Yii::app()->request->getParam('Service');
        if ($requestData && is_array($requestData)) {
            $model->attributes = $requestData;
            $services->name = Helper::getFromMap($requestData, 'name');
            $services->code = Helper::getFromMap($requestData, 'code');
            $services->profession_id = Helper::getFromMap($requestData, 'profession_id');
            $services->service_category_id = Helper::getFromMap($requestData, 'service_category_id');
        }
        $this->render('settingsForm', array(
            'model' => $model,
            'services' => $services,
            'id' => $id
        ));
    }

    /**
     *
     */
    public function actionTrash()
    {
        $model = new SalaryReport('search');
        $model->unsetAttributes();
        if (isset($_GET['SalaryReport'])) {
            $model->attributes = $_GET['SalaryReport'];
        }
        $this->render('trash', array('model' => $model));
    }

    /**
     * @return User
     */
    private function getListModel()
    {
        $model = new User('search');
        $model->unsetAttributes();
        if (isset($_GET['User'])) {
            $model->attributes = $_GET['User'];
        }
        return $model;
    }

    /**
     * @param $id
     */
    public function actionTotrash($id)
    {
        $item = SalaryReport::model()->getById($id);
        if ($item) {
            $item->toTrash();
            $this->setSuccess('Элемент перемещен в корзину');
        }
        $this->redirect(Yii::app()->createUrl('salary/default/index'));
    }

    /**
     * @param $id
     */
    public function actionFromtrash($id)
    {
        $item = SalaryReport::model()->getById($id);
        if ($item) {
            $item->fromTrash();
            $this->setSuccess('Элемент восстановлен из корзины');
        }
        $this->redirect(Yii::app()->createUrl('salary/default/index'));
    }
    /**
    * @param $id
     */
    public function actionReportClose($id)
    {
        /** @var SalaryReport $item */
        $item = SalaryReport::model()->getById($id);
                if ($item) {
                    $item->status = SalaryReport::STATUS_CLOSED;
                    $item->save();
                    $this->setSuccess('Расчет закрыт');
                }
        $this->redirect(Yii::app()->createUrl('salary/default/index'));
    }

    /**
     *
     */
    public function actionServices()
    {
        $this->module->css(array(
            'css/style.css'
        ));
        $this->render('services');
    }

    /**
     *
     */
    public function actionHours()
    {
        $this->module->css(array(
            'css/style.css'
        ));
        $this->render('hours');
    }
}