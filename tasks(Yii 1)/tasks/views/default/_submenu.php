<?
$this->submenu(array(
    array(
        'link' => 'tasks/default/create', 'visible' => $this->checkAccess('create'),
        'title' => 'Новая задача', 'icon' => 'ti-plus', 'class' => 'green'
    ),

    array(
        'link' => 'tasks/default/index',
        'title' => 'Все задачи', 'icon' => 'ti-list', 'visible' => $this->checkAccess('list')
    ),

    array(
        'link' => 'tasks/default/index?'.Task::model()->getUserTasksLink(),
        'title' => 'Назначенные на меня', 'icon' => 'ti-list', 'visible' => $this->checkAccess('list')
    ),

    array(
        'link' => 'tasks/default/index?Task[reporter_id]=' . Yii::app()->user->id  . '&Task[status]=' . Task::STATUS_EXTRA_NOT_CLOSED,
        'title' => 'Созданные мной', 'icon' => 'ti-list', 'visible' => $this->checkAccess('create')
    ),

));

