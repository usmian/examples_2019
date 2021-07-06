<?php

/**
 * This is the model class for table "task".
 *
 * The followings are the available columns in table 'task':
 * @property integer $task_id
 * @property string $title
 * @property string $desc
 * @property string $date_created
 * @property string $date_updated
 * @property integer $status
 * @property integer $reporter_id
 * @property integer $responsible_id
 * @property integer $performer_id
 * @property integer $performer_type
 * @property string $due_date
 * @property integer $project_id
 * @property integer $is_viewed
 * @property integer $type
 * @property integer $time
 */
class Task extends PModel
{
    const PERFORMER_TYPE_USER = 1;
    const PERFORMER_TYPE_ROLE = 2;

    const STATUS_NEW = 1;
    const STATUS_IN_WORK = 2;
    const STATUS_NEED_INFO = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_NOT_COMPLETED = 5;
    const STATUS_CLOSED = 6;

    const STATUS_EXTRA_ACTIVE = 'active';
    const STATUS_EXTRA_COMPLETED = 'completed';
    const STATUS_EXTRA_NOT_CLOSED = 'not_closed';

    const COMPLETE_EXPIRED = 1;
    const COMPLETE_LESS_THAN_HOUR = 2;

    const TYPE_TASK = 1;
    const TYPE_CALL = 2;

    public static $types = array(
        'my' => 'Мои задачи',
        'reports' => 'Созданные мной'
    );

    public static $tasksTypes = array(
        self::TYPE_TASK => 'Задание',
        self::TYPE_CALL => 'Звонок'
    );

    public static $performersTypes = array(
        self::PERFORMER_TYPE_USER => 'Сотрудник',
        self::PERFORMER_TYPE_ROLE => 'Должностная роль',
    );

    public static $completeTypes = array(
        self::COMPLETE_EXPIRED => 'время прошло',
        self::COMPLETE_LESS_THAN_HOUR => 'менее часа'
    );

    public $_reporter;
    public $_responsible;

    public $tempAttributes = array();
    public $historyFields = array('status', 'performer_id', 'responsible_id', 'due_date', 'type');

    public $modelTitle = 'Задача';
    public $completeTitle = 'Решить задачу';
    public $time;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'tasks';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('title, performer_type, performer_id', 'required'),
            array('title', 'length', 'max' => 256),
            array('desc, date_created, date_updated, due_date, is_viewed, responsible_id, status, type', 'safe'),
            array('
			    task_id, title, desc, date_created, date_updated, status,
			    reporter_id, performer_id, performer_type, due_date, project_id,
			    responsible_id, type',
                'safe', 'on' => 'search'
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'performer_user' => array(self::BELONGS_TO, 'UserController', 'performer_id'),
            'performer_role' => array(self::BELONGS_TO, 'Role', 'performer_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function labels()
    {
        return array(
            'task_id' => 'ID',
            'title' => 'Название',
            'desc' => 'Описание',
            'status' => 'Статус',
            'reporter_id' => 'Автор',
            'performer_id' => 'Исполнитель',
            'performer_type' => 'Тип исполнителя',
            'responsible_id' => 'Ответственный',
            'due_date' => 'Срок выполнения',
            'type' => 'Тип задачи',
            'complete' => '',
            'time' => 'Время'
        );
    }

    /**
     * @return array
     */
    public function getExcludeFields()
    {
        return array('is_viewed', 'performer_type');
    }

    /**
     * @return array|void
     */
    public function activityRewriteMap()
    {
        return array(
            'status' => 'getStatus',
            'reporter_id' => 'getReporter',
            'responsible_id' => 'getResponsible',
            'performer_id' => array('method' => 'getPerformer', 'args' => array('performer_id', 'performer_type')),
            'due_date' => 'getDueDate'
        );
    }

    /**
     * @return CActiveDataProvider
     */
    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->select = array('*', 'reporters.name as _reporter', 'responsible.name as _responsible');
        // todo
        //$criteria->select = array('*', 'tasks.task_id as _reporter', 'tasks.task_id as _responsible');
        /*$criteria->with = array(
            'performer_user' => array('resetScope' => true),
            'performer_role' => array('resetScope' => true)
        );*/
        $criteria->together = true;

        $criteria->join = $this->getAddJoin();

        if ($this->performer_id) {
            $performer = explode('_', $this->performer_id);
            if (isset($performer[1])) {
                $id = $performer[1];
                $type = $performer[0];
            } else {
                $type = self::PERFORMER_TYPE_USER;
                $id = $performer[0];
            }
            $criteria->compare('performer_id', $id);
            $criteria->compare('performer_type', $type);
        }

        $criteria->compare('title', $this->title, true);
        $criteria->compare('desc', $this->desc, true);

        if ($this->status && $this->isExtraStatus($this->status)) {
            $criteria->addInCondition('tasks.status', $this->getExtraStatusValues($this->status));
        } else {
            $criteria->compare('tasks.status', $this->status);
        }

        $criteria->compare('reporter_id', $this->reporter_id);
        $criteria->compare('responsible_id', $this->responsible_id);
        $criteria->compare('type', $this->type);
        //
        $criteria->compare('due_date', $this->due_date);

        //
        if (!UserController::model()->hasRole('admin')) {
            $criteria->addCondition($this->getPerformerCondition(true));
        }

        //
        if (!isset($_GET['Task_sort'])) {
            $criteria->order = 'tasks.is_viewed ASC, tasks.status ASC, tasks.date_updated DESC ';
        }

        //
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 15,
            ),
        ));
    }

    /**
     * @return string
     */
    private function getAddJoin()
    {
        return "
			LEFT OUTER JOIN (SELECT user_id as user_reporter_id, name FROM users) reporters ON reporters.user_reporter_id = tasks.reporter_id
			LEFT OUTER JOIN (SELECT user_id as user_responsible_id, name FROM users) responsible ON responsible.user_responsible_id = tasks.responsible_id
		";
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Task the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param $data
     * @return Task
     */
    public static function createByData($data)
    {
        $task = new Task();
        $task->attributes = $data;
        $task->due_date = $data['due_date'].' '.$data['dueTime'];
        return $task;
    }
    /**
     * @return bool|void
     */
    protected function afterFind()
    {
        $date = Helper::date($this->due_date, 'd.m.Y H:i');
        $this->due_date = $date;
        $this->time = Helper::date($this->due_date, 'H:i');
        $this->tempAttributes = $this->attributes;
        $this->tempAttributes['performer'] = $this->getPerformer();
        parent::afterFind();
    }

    /**
     * @return bool
     */
    protected function beforeSave()
    {
        if ($this->isNewRecord) {
            $this->reporter_id = Yii::app()->user->id;
            $this->status = self::STATUS_NEW;
        }

        //
        if (!$this->due_date) {
            $this->due_date = null;
        } else {
            $this->due_date = Helper::date($this->due_date, 'Y-m-d H:i');
        }

        //
        if (!$this->isNewRecord && ($this->performer_id != $this->tempAttributes['performer_id'] || $this->performer_type != $this->tempAttributes['performer_type'])) {
            $this->is_viewed = false;
        }

        //
        if ($this->isNewRecord && $this->isPerformer() && $this->isReporter()) {
            $this->is_viewed = true;
        }

        return parent::beforeSave();
    }

    /**
     *
     */
    protected function afterSave()
    {
        // Типа залогировали
        $this->saveHistory();
        parent::afterSave();
    }

    /**
     *
     */
    private function saveHistory()
    {
        if (empty($this->tempAttributes)) {
            return false;
        }
        $changes = array();

        foreach ($this->attributes as $key => $value) {
            if (!in_array($key, $this->historyFields)) {
                continue;
            }

            $tempKey = Helper::camelize(str_replace('_id', '', $key), false);
            $method = 'get' . $tempKey;
            if ($key !== 'performer_id') {
                try {
                    $value = $this->{$method}();
                } catch (Exception $e) {
                }
            } else {
                $value = $this->getPerformer($this->performer_id, $this->performer_type);
            }

            $prevValue = Helper::getFromMap($this->tempAttributes, $key);
            if ($prevValue) {
                if ($key !== 'performer_id') {
                    try {
                        $prevValue = $this->{$method}($prevValue);
                    } catch (Exception $e) {
                    }
                } else {
                    $prevValue = $this->getPerformer(null, null, true);
                }
            }

            if ($value != $prevValue) {
                $changes[] = array('key' => $key, 'value' => $value, 'prevValue' => $prevValue);
            }
            TaskHistory::model()->create($this->task_id, $key, $value, $prevValue);
        }
        //
        Yii::app()->getModule('tasks')->notification->handleUpdate(Yii::app()->user->id, $this, $changes);
    }

    /**
     * @return bool
     */
    public function isTimeDefault()
    {
        if ($this->time == '00:00') {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function completeTask()
    {
        $this->status = self::STATUS_COMPLETED;
        return $this->save();
    }

    /**
     * @param null $userId
     * @return bool
     */
    public function isPerformer($userId = null)
    {
        if (!$userId) {
            $userId = Yii::app()->user->id;
        }
        if ($this->performer_type == self::PERFORMER_TYPE_USER && $this->performer_id == $userId) {
            return true;
        }
        if ($this->performer_type == self::PERFORMER_TYPE_ROLE && UserController::model()->hasRoleById($this->performer_id)) {
            return true;
        }
        return false;
    }

    /**
     * @param null $userId
     * @return bool
     */
    public function isResponsible($userId = null)
    {
        if (!$userId) {
            $userId = Yii::app()->user->id;
        }
        return $this->responsible_id == $userId;
    }

    /**
     * @param null $userId
     * @return bool
     */
    public function isReporter($userId = null)
    {
        if (!$userId) {
            $userId = Yii::app()->user->id;
        }
        return $this->reporter_id == $userId;
    }

    /**
     * @return bool
     */
    public function canEdit()
    {
        if ($this->isNewRecord) {
            return true;
        }
        return $this->isReporter();
    }

    /**
     * @return bool
     */
    public function canView()
    {
        if ($this->isNewRecord) {
            return true;
        }
        return $this->isReporter() || $this->isPerformer() || $this->isResponsible() || UserController::model()->hasRole('admin');
    }

    /**
     * @param null $id
     * @param null $type
     * @param bool $getTemp
     * @return null|string
     */
    public function getPerformer($id = null, $type = null, $getTemp = false)
    {
        if ($getTemp) {
            return $this->tempAttributes['performer'];
        }

        //
        if ($id) {
            return ($type == self::PERFORMER_TYPE_USER) ?
                Helper::getShortName(UserController::model()->getNameById($id)) :
                Role::model()->getNameById($id);
        }

        //
        if ($this->performer_type == self::PERFORMER_TYPE_USER) {
            return Helper::getShortName($this->performer_user->name);
        }
        if ($this->performer_type == self::PERFORMER_TYPE_ROLE) {
            return $this->performer_role->title;
        }
        return '';
    }

    /**
     * @param bool $responsible
     * @return string
     */
    public function getPerformerCondition($responsible = false)
    {
        $roles = implode(",", UserRole::model()->getIds(Yii::app()->user->id));
        if (!$roles) {
            $roles = '0';
        }
        $result = '(
                  (performer_type = ' . Task::PERFORMER_TYPE_ROLE . ' AND performer_id IN (' . $roles . ') ) OR
                  (performer_type = ' . Task::PERFORMER_TYPE_USER . ' AND performer_id = ' . Yii::app()->user->id . ')
                ';
        if ($responsible) {
            $result .= 'OR responsible_id IN (' . Yii::app()->user->id . ')';
        }
        $result .= 'OR reporter_id IN (' . Yii::app()->user->id . ') ';
        $result .= ')';
        return $result;
    }

    /**
     * @param $id
     * @param int $type
     * @return CActiveRecord|null
     */
    public function getByPerformer($id, $type = null)
    {
        if (!$type) {
            $type = self::PERFORMER_TYPE_USER;
        }
        $cr = new CDbCriteria();
        $cr->compare('performer_type', $type);
        $cr->compare('performer_id', $id);
        return $this->find($cr);
    }

    /**
     * @param null $id
     * @return mixed
     */
    public function getReporter($id = null)
    {
        if (!$id) {
            $id = $this->reporter_id;
        }
        return Helper::getShortName(UserController::model()->getNameById($id));
    }

    /**
     * @param $id
     * @return null
     */
    public function getResponsible($id = null)
    {
        if (!$id) {
            $id = $this->responsible_id;
        }
        return ($id) ? UserController::model()->getNameById($id) : null;
    }

    /**
     * @param null $date
     * @return bool|string
     */
    public function getDueDate($date = null)
    {
        if (!$date) {
            $date = $this->due_date;
        }
        $time = substr_replace($date, null, 0, 11);
        if ($time == '00:00') {
            return Helper::date($date, 'd.m.Y');
        }
        return Helper::date($date, 'd.m.Y H:i');
    }

    /**
     * @return array
     */
    public function getDueDatesList()
    {
        $result = array();
        $cr = new CDbCriteria();

        $data = Task::model()->findAll($cr);
        foreach ($data as $item) {
            $date = Helper::date($item->due_date, 'd.m.Y');
            $result[$date] = $date;
        }
        return $result;
    }


    /**
     * @return array
     */
    public function getComboPerformersList()
    {
        $result = array();
        $users = UserController::model()->getDropDown();
        $roles = Role::model()->getDropDown(true);
        foreach ($users as $i => $u) {
            $result[] = array('id' => self::PERFORMER_TYPE_USER . '_' . $i, 'text' => $u, 'group' => 'Сотрудник');
        }
        foreach ($roles as $i => $r) {
            $result[] = array('id' => self::PERFORMER_TYPE_ROLE . '_' . $i, 'text' => $r, 'group' => 'Должностная роль');
        }
        return CHtml::listData($result, 'id', 'text', 'group');

    }

    /**
     * @return array
     */
    public function getTypesList()
    {
        return self::$tasksTypes;
    }

    /**
     * @param $status
     * @return mixed
     */
    public function getTypeCompleted($status)
    {
        return self::$completeTypes[$status];
    }

    /**
     * @param bool $all
     * @param bool $extra
     * @return array|mixed
     */
    public function getStatusList($all = false, $extra = false)
    {
        $list = array();
        if ($extra) {
            $list = CMap::mergeArray($list, $this->getExtraStatusList());
        }
        $list = CMap::mergeArray($list, array(
            self::STATUS_NEW => 'Новая',
            self::STATUS_IN_WORK => 'В работе',
            self::STATUS_COMPLETED => 'Решена',
        ));
        if ($this->isReporter() || $all || $this->status == self::STATUS_CLOSED) {
            $list[self::STATUS_CLOSED] = 'Закрыта';
            $list[self::STATUS_NEED_INFO] = 'Требуется информация';
            $list[self::STATUS_NOT_COMPLETED] = 'Невозможно решить';
        }
        return $list;
    }

    /**
     * @param bool $short
     * @return array
     */
    public function getExtraStatusList($short = true)
    {
        $list = array(
            self::STATUS_EXTRA_COMPLETED => array(
                'title' => 'Решенные',
                'status' => array(/*self::STATUS_NOT_COMPLETED,*/
                    self::STATUS_COMPLETED, self::STATUS_CLOSED)
            ),
            self::STATUS_EXTRA_ACTIVE => array(
                'title' => 'Активные',
                'status' => array(self::STATUS_NEW, self::STATUS_IN_WORK, /*self::STATUS_COMPLETED, self::STATUS_NEED_INFO*/)
            ),

            self::STATUS_EXTRA_NOT_CLOSED => array(
                'title' => 'Незакрытые',
                'status' => array(self::STATUS_NEW, self::STATUS_IN_WORK, self::STATUS_NEED_INFO, self::STATUS_NOT_COMPLETED,
                    self::STATUS_COMPLETED)
            )
        );
        if (!$short) {
            return $list;
        }
        $result = array();
        foreach ($list as $k => $v) {
            $result[$k] = $v['title'];
        }
        return $result;
    }

    /**
     * @param bool $all
     * @return array|mixed
     */
    public function getStatusTitles($all = false)
    {
        $list = array();
        if (!$all) {
            $list = CMap::mergeArray($list, $this->getExtraStatusListTitle());
        }
        $list = CMap::mergeArray($list, array(
            self::STATUS_NEW => 'Новая',
            self::STATUS_IN_WORK => 'В работе',
        ));
        if ($all) {
            $list = CMap::mergeArray($list, array(
                self::STATUS_COMPLETED => 'Решена',
            ));
        }
        return $list;
    }

    /**
     * @param bool $short
     * @return array
     */
    public function getExtraStatusListTitle($short = true)
    {
        $list = array(
            self::STATUS_EXTRA_ACTIVE => array(
                'title' => 'Активные',
                'status' => array(self::STATUS_NEW, self::STATUS_IN_WORK)
            ),
            self::STATUS_EXTRA_COMPLETED => array(
                'title' => 'Решенные',
                'status' => array(self::STATUS_COMPLETED, self::STATUS_CLOSED)
            ),
        );
        if (!$short) {
            return $list;
        }
        $result = array();
        foreach ($list as $k => $v) {
            $result[$k] = $v['title'];
        }
        return $result;
    }

    /**
     * @return bool|mixed
     */
    public function getCompleteStatus()
    {
        if ($this->isTaskExpired()) {
            return $this->getTypeCompleted(self::COMPLETE_EXPIRED);
        }

        if ($this->isTaskLessThanHour()) {
            return $this->getTypeCompleted(self::COMPLETE_LESS_THAN_HOUR);
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isTaskExpired()
    {
        $now = new DateTime();
        $tomorrow = $now->format('Y-m-d');

        $due = new DateTime($this->due_date);
        $dueDate = $due->format('Y-m-d');
        $compare = new DateTime();
        $time = $this->time;

        if ($time == '00:00' && $tomorrow > $dueDate
            && $this->status != self::STATUS_COMPLETED
            && $this->status != self::STATUS_CLOSED) {
            return true;
        }
        if ($due < $compare && $this->status != self::STATUS_COMPLETED
            && $this->status != self::STATUS_CLOSED
            && $time != '00:00') {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isTaskLessThanHour()
    {
        $now = new DateTime();
        $due = new DateTime($this->due_date);
        $diff = $now->diff($due);
        if ($diff->days == 0 && $diff->h == 0 && $diff->i > 0
            && $this->status != self::STATUS_COMPLETED
            && $this->status != self::STATUS_CLOSED) {
            return true;
        }
        return false;
    }

    /**
     * @return int|string
     */
    public function getTaskDateFormatView()
    {
        $html = CHtml::tag("span", $this->getTooltip()
            , CHtml::encode($this->getBeautyDate()));
        return $html;
    }

    /**
     * @return array
     */
    private function getTooltip()
    {
        $expired = "has-tooltip status-expired";
        $hour = "has-tooltip status-hour";
        $none = "";
        $title = $this->getCompleteStatus();

        return $this->isTaskExpired() ? array("class" => $expired, "title" => $title) : (
        $this->isTaskLessThanHour() ? array("class" => $hour, "title" => $title)
            : array("class" => $none, "title" => $title));
    }

    /**
     * @return int|string
     */
    public function getBeautyDate()
    {
        return Helper::getBeautyDate(
            Helper::date($this->due_date, "d.m.Y H:i"), true, false, false,
            ($this->isTimeDefault() ? false : true), false);
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status == self::STATUS_COMPLETED
            || $this->status == self::STATUS_CLOSED
            || $this->needView()
            || !($this->isPerformer());
    }

    /**
     * @param $status
     * @return bool
     */
    public function isExtraStatus($status)
    {
        $list = $this->getExtraStatusList();
        return array_key_exists($status, $list);
    }

    /**
     * @param $status
     * @return mixed
     */
    public function getExtraStatusValues($status)
    {
        $list = $this->getExtraStatusList(false);
        return $list[$status]['status'];
    }

    /**
     * @param null $status
     * @return null
     */
    public function getStatus($status = null)
    {
        if (!$status) {
            $status = $this->status;
        }
        return Helper::getFromMap($this->getStatusList(true), $status);
    }

    /**
     * @param null $type
     * @return null
     */
    public function getType($type = null)
    {
        if (!$type) {
            $type = $this->type;
        }
        return Helper::getFromMap($this->getTypesList(), $type);
    }


    /**
     * @return bool
     */
    public function needView()
    {
        if (!$this->is_viewed && $this->isPerformer() && $this->status != self::STATUS_COMPLETED) {
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function handleViewed()
    {
        if ($this->isNewRecord || $this->is_viewed) {
            return;
        }
        if ($this->isPerformer()) {
            $this->updateByPk($this->task_id, array('is_viewed' => true));
        }
    }

    /**
     * @return CDbDataReader|mixed|string
     */
    public function getNewTasksCount()
    {
        $cr = new CDbCriteria();
        $cr->addCondition($this->getPerformerCondition());
        $cr->addCondition('tasks.is_viewed is false');
        $tasks = Task::model()->findAll($cr);
        $count = 0;
        /** @var Task $t */
        foreach ($tasks as $t) {
            if ($t->isPerformer()) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * @param bool $active
     * @return string
     */
    public function getUserTasksLink($active = true)
    {
        $link = 'Task[performer_id]=' . Task::PERFORMER_TYPE_USER . '_' . Yii::app()->user->id;
        if ($active) {
            $link .= '&Task[status]=' . Task::STATUS_EXTRA_ACTIVE;
        }
        return $link;
    }
}
