<?php

/**
 *
 */
class TaskNotification extends CApplicationComponent
{
    /**
     * @param Task $task
     * @return bool
     */
    public function handleCreate($task) {
        $emails = $this->getEmails($task, false);
        if (empty($emails)) {
            return false;
        }
        $params = array(
            'title' => $task->title,
            'desc' => str_replace("\n", "<br/>", $task->desc),
            'date' => Helper::date($task->date_created, 'd.m.Y H:i'),
            'performer' => $task->getPerformer(),
            'reporter' => $task->getReporter(),
            'responsible' => $task->getResponsible(),
            'due' => Helper::date($task->due_date, 'd.m.Y'),
            'url' => Yii::app()->getBaseUrl(true) .'/tasks/default/update/id/' . $task->task_id
        );
        Yii::app()->mailer->send($emails, 'Новая задача - ' . $task->title, array(
            'new_task', $params
        ));
    }

    /**
     * @param $userId
     * @param $task
     * @param $fields
     * @return bool
     */
    public function handleUpdate($userId, $task, $fields) {
        $emails = $this->getEmails($task, true, $userId);
        if (empty($emails) || empty($fields)) {
            return false;
        }

        $user = UserController::model()->getById($userId);
        $labels = Task::model()->labels();
        foreach ($fields as $i => $f) {
            $fields[$i]['key'] = $labels[$fields[$i]['key']];
        }
        $params = array(
            'UserController' => $user->name,
            'url' => Yii::app()->getBaseUrl(true) .'/tasks/default/update/id/' . $task->task_id,
            'title' => $task->title,
            'fields' => $fields
        );

        Yii::app()->mailer->send($emails, 'Изменения в задаче - ' . $task->title, array(
            'update_task', $params
        ));
    }

    /**
     * @param $task
     * @param bool $withReporter
     * @param null $author
     * @return array
     */
    private function getEmails($task, $withReporter = true, $author = null) {
        $list = array();

        //
        if ($task->performer_type == Task::PERFORMER_TYPE_USER && $task->performer_id != $author) {
            $email = UserController::model()->getEmailById($task->performer_id);
            if ($email) {
                $list[] = $email;
            }
        }
        if ($task->performer_type == Task::PERFORMER_TYPE_ROLE) {
            $list = UserController::model()->getEmailsByRoleId($task->performer_id);
        }

        //
        if ($task->responsible_id && $task->responsible_id != $author) {
            $email = UserController::model()->getEmailById($task->responsible_id);
            if ($email) {
                $list[] = $email;
            }
        }

        //
        if ($withReporter && $task->reporter_id != $author) {
            $email = UserController::model()->getEmailById($task->reporter_id);
            if ($email) {
                $list[] = $email;
            }
        }

        //
        return array_unique($list);
    }
}