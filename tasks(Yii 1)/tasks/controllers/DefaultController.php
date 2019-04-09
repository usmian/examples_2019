<?php

class DefaultController extends MController
{

    /**
     * @throws CHttpException
     */
    public function actionIndex() {
        //
        $this->module->css(array(
            'css/style.css'
        ));

        $model = new Task('search');
        $model->unsetAttributes();
        $model->status = Task::STATUS_EXTRA_ACTIVE;
        if(isset($_GET['Task'])) {
            $model->attributes = $_GET['Task'];
        }
        $this->render('index', array(
            'model' => $model
        ));
    }


    /**
    /**
     *
     */
    public function actionCreate() {
        $this->form('create');
    }

    /**
     * @param $id
     */
    public function actionUpdate($id) {
        $this->form('update', $id);
    }

    /**
     * @param $action
     * @param null $id
     * @throws CHttpException
     */
    private function form($action, $id = null) {
        $model = ($action === 'update') ? Task::model()->getById($id) : new Task;
        if (!$model || !$model->canView()) {
            throw new CHttpException(404);
        }

        $this->module->css(array(
            'css/style.css'
        ));

        //
        if (!$model->performer_type) {
            $model->performer_type = Task::PERFORMER_TYPE_USER;
        }
        $model->handleViewed();

        $users = User::model()->getDropDown();
        $roles = Role::model()->getDropDown();
        $types = Task::$tasksTypes;

        $performers = ($model->performer_type === Task::PERFORMER_TYPE_USER) ? $users : $roles;

        $history = ($action === 'update') ? TaskHistory::model()->getByTask($model->task_id) : null;

        if (isset($_POST['Task'])) {
            $model->attributes = $_POST['Task'];
            if ($model->validate()) {
                $model->save();

                if ($action === 'create') {
                    $this->module->notification->handleCreate($model);
                }

                $this->setSuccess(($action === 'create') ? 'Задача успешно добавлена': 'Изменения сохранены');
                $this->redirect('/tasks');
            }
        }
        if($model->isTimeDefault()){
            $model->time = 'чч:мм';
        }
        //
        $this->render('form', array(

            'action' => $action,
            'id' => $id,
            'model' => $model,
            'performers' => $performers, 'users' => $users, 'roles' => $roles,
            'types'=>$types,
            'history' => $history
        ));
    }
}