<?php

class TasksModule extends Module
{

	public $noAccessControl = true;
	public $commentsModels = array('Task');
	public $filesModels = ['Task'];

	/**
	 *
	 */
	public function init()
	{
		parent::init();
		$this->setComponent('notification', new TaskNotification());
	}

	/**
	 * @return array|void
	 */
	public function additionalLevels()
	{
		return array(
			'comment' => 'Комментарии',
			'files' => 'Файлы'
		);
	}

}