<?php

/**
 * This is the model class for table "task_history".
 *
 * The followings are the available columns in table 'task_history':
 * @property integer $task_history_id
 * @property integer $task_id
 * @property integer $user_id
 * @property string $field
 * @property string $value
 * @property string $date_created
 * @property integer $project_id
 */
class TaskHistory extends PModel
{
    public $saveActivity = false;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'task_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('task_history_id, task_id, user_id, field, value, prev_value, date_created, project_id', 'safe'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'users' => array(self::BELONGS_TO, 'UserController', 'user_id')
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'task_history_id' => 'Task History',
			'task_id' => 'Task',
			'user_id' => 'UserController',
			'status' => 'Status',
			'date_created' => 'Date Created',
			'project_id' => 'Project',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TaskHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * @param $id
     * @param $field
     * @param $value
     * @param null $prevValue
     * @return bool
     */
    public function create($id, $field, $value, $prevValue = null) {
        if ($value == $prevValue) {
            return true;
        }
        $model = new TaskHistory();
        $model->attributes = array(
            'task_id' => $id,
            'user_id' => Yii::app()->user->id,
            'field' => $field,
            'value' => $value,
            'prev_value' => $prevValue
        );
        return $model->save();
    }

    /**
     * @param $id
     * @return CActiveRecord[]
     */
    public function getByTask($id) {
        $cr = new CDbCriteria();
        $cr->with = array('users' => array('resetScope' => true));
        $cr->together = true;
        $cr->compare('task_id', $id);
        $cr->order =  'task_history.date_created DESC';
        return $this->findAll($cr);
    }

    /**
     * @return null
     */
    public function getFieldTitle() {
        $labels = Task::model()->labels();
        return Helper::getFromMap($labels, $this->field);
    }
}
