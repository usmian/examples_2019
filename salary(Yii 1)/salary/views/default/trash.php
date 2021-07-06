<?php
$this->pageTitle = 'Корзина - Зарплаты - ' . Yii::app()->name;
$this->breadcrumb(array('Зарплаты' => '/salary/default/index', 'Корзина'));
?>


<? $this->renderPartial('_menu'); ?>
    <div class="panel">
        <div class="panel-body">
            <?
            $this->widget('bootstrap.widgets.BsGridView', array(
                    'type' => array(BSHtml::GRID_TYPE_STRIPED, BSHtml::GRID_TYPE_HOVER),
                    'dataProvider' => $model->search(true),
                    'filter' => null,
                    'ajaxUpdate' => true,
                    'afterAjaxUpdate' => 'js:function() 
                                 {
                                    reportsSalary.init();
                                 }',
                    'template' => "{summary}{pager}\n{items}\n{pager}",
                    'columns' => array(
                        'num' => array('name' => 'num', 'value' => '$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)', 'htmlOptions' => array('width' => '5%'), 'filter' => false),
                        array('type' => 'raw', 'name' => 'date_from',
                            'value' => '$data->getDatesTitle()',
                            'htmlOptions' => array('width' => '10%')
                        ),
                        array('type' => 'raw', 'name' => 'user_id',
                            'value' => '$data->getName()',
                            'htmlOptions' => array('width' => '10%')
                        ),
                        array('type' => 'raw', 'name' => 'date_updated',
                            'value' => '$data->getDateUpdated()',
                            'htmlOptions' => array('width' => '10%')
                        ),
                        array(
                            'type' => 'raw', 'name' => '', 'htmlOptions' => array('width' => '10%', 'class' => 'text-right', 'style' => 'white-space: nowrap;'), 'header' => '', 'filter' => false, 'sortable' => false,
                            'value' => '
                        "<a class=\'has-tooltip from-trash pull-right\' style=\'color: #777\' title=\'Восстановить\' href=\'".Yii::app()->createUrl("salary/default/fromtrash/id/".$data->salary_report_id)."\'><span class=\'ti-back-left\'></span></a>"'
                        ),
                    ),
                    'enableSorting' => true,
                    'emptyText' => 'Расчеты не найдены',
                    'ajaxUrl' => Yii::app()->createUrl('salary/default/trash')
                )
            );
            ?>
        </div>
    </div>

<? $this->scripts($this->module->assetsUrl . '/js/reportsSalary.js', true); ?>
