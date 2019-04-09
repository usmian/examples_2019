<?php
$this->pageTitle = 'Задачи - ' . Yii::app()->name;

$breadcrumbs = array('Задачи' => '/tasks', 'Список задач');
$this->breadcrumb($breadcrumbs);
?>

<? $this->renderPartial('_submenu'); ?>

<div class="panel">

    <div class="panel-body">


        <?

        $this->widget('bootstrap.widgets.BsGridView', array(
                'type' => array(BSHtml::GRID_TYPE_HOVER, BSHtml::GRID_TYPE_STRIPED),
                'dataProvider' => $model->search(),
                'filter' => $model,
                'ajaxUpdate' => true,
                'template' => "{pager}\n{items}\n{pager}",
                'rowCssClassExpression' => '"status-" . $data->status . ( ($data->needView()) ? " not-viewed" : " ")',
                'columns' => array(
                    'num' => array('name' => 'num', 'value' => '$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)', 'htmlOptions' => array('width' => '2%'), 'filter' => false),
                    //array('type'=>'raw', 'name'=> 'task_id', 'htmlOptions'=>array('width'=>'3%')),
                    array(
                        'type' => 'raw', 'name' => 'status', 'htmlOptions' => array('width' => '10%'),
                        'value' => 'mb_strtolower($data->getStatus(), "utf-8")',
                        'filter' => BSHtml::activeDropDownList($model, 'status', Task::model()->getStatusTitles(), array('class' => 'form-control', "empty" => ""))
                    ),
                    array(
                        'type' => 'raw', 'name' => 'title', 'htmlOptions' => array('width' => '15%', 'class' => 'title'),
                        'value' => '"<a href=\'".Yii::app()->createUrl("/tasks/default/update/id/" . $data->task_id)."\' class=\'status-".$data->status."\'>" .$data->title. "</a>"'
                    ),

                    array(
                        'type' => 'raw', 'name' => 'performer_id', 'htmlOptions' => array('width' => '10%'), 'value' => '$data->getPerformer()',
                        'filter' => BSHtml::activeDropDownList($model, 'performer_id', Task::model()->getComboPerformersList(), array('class' => 'form-control', "empty" => ""))
                    ),

                    array(
                        'type' => 'raw', 'name' => 'reporter_id', 'value' => 'Helper::getShortName($data->_reporter)', 'htmlOptions' => array('width' => '10%'),
                        'filter' => BSHtml::activeDropDownList($model, 'reporter_id', User::model()->getDropDown(), array('class' => 'form-control', "empty" => ""))
                    ),

                    array(
                        'type' => 'raw', 'name' => 'responsible_id', 'htmlOptions' => array('width' => '10%'), 'value' => 'Helper::getShortName($data->_responsible)',
                        'filter' => BSHtml::activeDropDownList($model, 'responsible_id', User::model()->getDropDown(), array('class' => 'form-control', "empty" => ""))
                    ),
                    array(
                        'type' => 'raw', 'name' => 'type', 'htmlOptions' => array('width' => '10%'), 'value' => '$data->getType()',
                        'filter' => BSHtml::activeDropDownList($model, 'type', Task::model()->getTypesList(), array('class' => 'form-control', "empty" => ""))
                    ),
                    array(
                        'type' => 'raw', 'name' => 'due_date', 'htmlOptions' => array('width' => '10%'),
                        'value' => '$data->getTaskDateFormatView()',
                        'filter' => false
                        //'filter' => Helper::rangeFilter($model, 'due_date'),
                    ),
                    array(
                        'type' => 'raw', 'name' => 'complete',
                        'value' => '$data->isCompleted() ? "" : 
                        "<div data-id=\'".$data->task_id."\' class=\'btn btn-sm btn-default pull-right ti-check task-confirm has-tooltip\' title=\'".$data->completeTitle."\'></div>"',
                        'htmlOptions' => array('width' => '7%'), 'filter' => false
                    ),
                    /* array(
                         'type'=>'raw', 'name'=> 'date_updated', 'value' => 'Helper::date($data->date_updated, "d.m.Y H:i")', 'htmlOptions'=>array('width'=>'7%'), 'filter' => false
                     ),*/
                ),
                'enableSorting' => true,
                'emptyText' => 'Задачи не найдены'
            )
        );
        ?>
    </div>
</div>
<? $this->scripts($this->module->assetsUrl . '/js/tasksList.js', true) ?>

