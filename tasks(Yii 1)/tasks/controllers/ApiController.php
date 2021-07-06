<?php

class ApiController extends AController
{
    /**
     * @return array
     */
    protected function actionsMap()
    {
        return array();
    }

    /**
     *
     */
    public function actionComment()
    {
        $id = Yii::app()->request->getParam('id');
        /** @var Task $task */
        $task = Task::model()->getById($id);
        if (!$task || !$task->canView()) {
            throw new CHttpException(404);
        }
        parent::actionComment();
    }

    /**
     * @return mixed
     * @throws CHttpException
     */
    public function actionComplete()
    {
        $id = Yii::app()->request->getParam('id');
        /** @var Task $task */
        $task = Task::model()->getById($id);
        if (!$task || !$task->canView()) {
            $this->error(404);
        }
        //
        $result = $task->completeTask();
        $this->response($result);
    }

    /**
     * @return mixed
     * @throws CHttpException
     */
    public function actionCreate()
    {
        $data = Yii::app()->request->getPost('data');
        $model = Task::createByData($data);

        if (!$model->save()) {
            $this->error(500);
        }

        $this->module->notification->handleCreate($model);
        $this->response(true);
    }

    /**
     * @return mixed
     */
    public function actionGetPerformers()
    {
        $type = Yii::app()->request->getParam('type');
        $types = Task::$performersTypes;
        $users = UserController::model()->getDropDown();
        $roles = Role::model()->getDropDown(true);

        $performers = ($type == Task::PERFORMER_TYPE_USER) ? $users : $roles;

        $this->response(array(
            'types' => $types,
            'performers' => $performers
        ));
    }
}
