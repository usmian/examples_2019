<?php
$titles = array('create' => 'Новая задача', 'update' => 'Просмотр задачи');
$this->pageTitle = $titles[$action] . ' - Задачи - ' . Yii::app()->name;
$this->breadcrumb(array('Задачи' => '/tasks', $titles[$action]));
?>



<? if ($action === 'update'): ?>
    <?
    $this->submenu(array(
        array(
            'link' => 'tasks',
            'title' => 'Задачи', 'icon' => 'ti-angle-left'
        ),
        array(
            'link' => '/tasks/default/update/id/'. $model->task_id,
            'title' => 'Редактирование задачи', 'icon' => ''
        ),
    ));
    ?>
<?else:?>
    <?
    $this->submenu(array(
        array(
            'link' => 'tasks',
            'title' => 'Задачи', 'icon' => 'ti-angle-left'
        ),
        array(
            'link' => '/tasks/default/create/',
            'title' => 'Новая задача', 'icon' => ''
        ),
    ));
    ?>
<?endif;?>

<?php $form = $this->beginWidget('bootstrap.widgets.BsActiveForm', array(
    'id' => 'createUpdateServiceCategory',
    'enableAjaxValidation' => false,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
    ),
    'htmlOptions' => array('class' => ''),
)); ?>

<div class="row">
    <div class="col-md-7">
        <div class="panel panel-default">
            <div class="panel-heading">Описание</div>
            <div class="panel-body">
                <?php echo $form->textFieldControlGroup($model, 'title', array('readonly' => (!$model->canEdit() ? 'readonly' : false))); ?>

                <?php echo $form->textAreaControlGroup($model, 'desc', array('rows' => 8, 'readonly' => (!$model->canEdit() ? 'readonly' : false))); ?>
            </div>
        </div>

        <? if ($this->checkAccess('files') && !$model->isNewRecord) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">Файлы</div>
                <div class="panel-body">
                    <? $this->renderPartial('//layouts/catalogs/files', [
                        'model' => get_class($model), 'id' => $model->task_id, 'controller' => 'tasks/api'
                    ]) ?>
                </div>
            </div>
        <? } ?>

        <? if ($this->checkAccess('comment') && !$model->isNewRecord) { ?>
            <? $this->renderPartial('//layouts/catalogs/comments', array('model' => 'Task', 'id' => $id, 'controller' => 'tasks/api')); ?>
        <? } ?>

    </div>

    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">Детали выполнения</div>
            <div class="panel-body">

                <? if (!$model->isNewRecord) { ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $form->dropDownListControlGroup($model, 'status', $model->getStatusTitles(true), array('class' => 'status')); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $form->textFieldControlGroup($model, 'reporter_id', array('value' => $model->getReporter(), 'name' => 'reporter', 'readonly' => 'readonly')); ?>
                        </div>
                    </div>
                    <hr/>
                <? } ?>

                <div class="row">
                    <div class="col-md-6">
                        <?php echo $form->dropDownListControlGroup($model, 'performer_type', Task::$performersTypes, array('class' => 'performer-type')); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo $form->dropDownListControlGroup($model, 'performer_id', $performers, array('empty' => '', 'class' => 'performers')); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php echo $form->dropDownListControlGroup($model, 'responsible_id', $users, array('class' => 'responsible', 'empty' => '')); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo $form->dropDownListControlGroup($model, 'type', $types, array('class' => 'type', 'empty' => '')); ?>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-7 inline">
                                <?php echo $form->textFieldControlGroup($model, 'due_date', array('class' => 'due-date', 'readonly' => (!$model->canEdit() ? 'readonly' : false),
                                    'placeholder' => ' ')); ?>
                            </div>
                            <div class="col-md-5 inline">
                                <label>&nbsp;</label>
                                <div class="no-label">
                                    <?php echo $form->textFieldControlGroup($model, 'time', array('class' => 'due-time', 'readonly' => (!$model->canEdit() ? 'readonly' : false),
                                        'placeholder' => ' ')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <? if (!$model->isNewRecord) { ?>
            <div class="panel panel-default task-history">
                <div class="panel-heading">История изменений</div>
                <div class="panel-body">
                    <div class="task-history-content">
                        <? if ($history && !empty($history)) { ?>
                            <? foreach ($history as $item) { ?>
                                <div class="callout callout-info">
                                    <p>
                                        <span class="time"><?= Helper::date($item->date_created, 'd.m.Y H:i') ?></span>
                                        <span class="user"><?= Helper::getShortName($item->users->name) ?></span>
                                        изменил(а) поле
                                        <span class="field">"<?= $item->getFieldTitle() ?>"</span>:
                                        <? if ($item->prev_value) { ?><span
                                                class="prev-value"><?= $item->prev_value ?></span><? } ?>
                                        <span class="value"><?= $item->value ?></span>
                                    </p>
                                </div>
                            <? } ?>
                        <? } ?>
                        <div class="callout callout-info">
                            <p>
                                <span class="time"><?= Helper::date($model->date_created, 'd.m.Y H:i') ?></span>
                                <span class="user"><?= $model->getReporter() ?></span> создал(а) задачу.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>
    </div>
</div>

<div class="panel panel-buttons">
    <div class="panel-body">
        <div class="submit">
            <?php echo CHtml::submitButton('Сохранить', array('class' => 'btn btn-success')); ?>
            <a class="btn btn-default " href="<?= Yii::app()->createUrl('/tasks') ?>">Отмена</a>
        </div>
    </div>
</div>

<?php $this->endWidget(); ?>

<input type="hidden" id="users-data" value='<?= json_encode($users) ?>'/>
<input type="hidden" id="roles-data" value='<?= json_encode($roles) ?>'/>

<? $this->scripts($this->module->assetsUrl . '/js/tasks.js', true) ?>
