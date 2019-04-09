<?php
$this->pageTitle = 'Настройка - Зарплаты - ' . Yii::app()->name;
$this->breadcrumb(array('Зарплаты' => '/salary', 'Настройка'));
?>


<? $this->renderPartial('_menu'); ?>

<div class="panel">
    <div class="panel-body">
        <?
        $this->widget('bootstrap.widgets.BsGridView', array(
                'type' => array(BSHtml::GRID_TYPE_STRIPED, BSHtml::GRID_TYPE_HOVER),
                'dataProvider' => $model->search(),
                'filter' => $model,
                'ajaxUpdate' => true,
                'template'=>"{summary}{pager}\n{items}\n{pager}",
                'columns' => array(
                    'num'=> array('name' => 'num','value' => '$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)','htmlOptions'=>array('width'=>'4%'),'filter'=>false),

                    array('name' => 'name', 'header' => '', 'type' => 'raw', 'value' => 'Avatar::getImageBlockByModel($data, true, null, "icon")', 'filter' => false, 'sortable' => false, 'htmlOptions'=>array('width'=>'3%')),

                    array('type'=>'raw', 'name'=> 'name', 'value'=>'
                        (!Yii::app()->controller->checkAccess(\'update\')) ? $data->name:"<a href=\'".Yii::app()->createUrl("salary/default/settingsUpdate", array("id" => $data->user_id))."\'>".$data->name."</a>"
                        ',  'htmlOptions'=>array('width'=>'15%')),

                    array('type'=>'raw', 'name'=> 'roleTitles', 'htmlOptions'=>array('width'=>'15%'), 'filter'=>BsHtml::activeDropDownList($model, 'roles', Role::model()->getListInArray(true, true, true), array("prompt"=>"", 'class'=>'form-control'))),

                    array('type'=>'raw', 'name'=> 'professionTitles', 'htmlOptions'=>array('width'=>'15%'), 'filter'=>BsHtml::activeDropDownList($model, 'professions', Profession::model()->getList(true), array("prompt"=>"", 'class'=>'form-control'))),

                    //array('type'=>'raw', 'name'=> 'date_created', 'value' => 'Helper::date($data->date_created, "d.m.Y H:i")', 'htmlOptions'=>array('width'=>'15%'), 'filter'=> Helper::rangeFilter($model, 'date_created') ),
                    array('type'=>'raw', 'name'=> 'salary', 'htmlOptions'=>array('width'=>'15%'),'value'=>'$data->getSalaryValue()', 'filter'=>''),
                ),
                'enableSorting'=>true,
                'emptyText'=>'Сотрудники не найдены',
                'ajaxUrl'=> Yii::app()->createUrl('salary/default/settings')
            )
        );
        ?>
    </div>
</div>