<?php
$this->pageTitle = 'Зарплаты - ' . Yii::app()->name;
$this->breadcrumb(array('Зарплаты'));
?>

<? if ($this->checkAccess('manage')) {?>
    <? $this->renderPartial('_menu'); ?>
<?}?>

    <div class="panel">
        <div class="panel-body">
            <?
            $this->widget('bootstrap.widgets.BsGridView', array(
                    'type' => array(BSHtml::GRID_TYPE_STRIPED, BSHtml::GRID_TYPE_HOVER),
                    'id' =>'grid-reports',
                    'dataProvider' => $model->search(false, (!$this->checkAccess('manage') ? Yii::app()->user->id : null)),
                    'filter' => $model,
                    'ajaxUpdate' => true,
                    'template' => "{summary}{pager}\n{items}\n{pager}",
                    'columns' => array(
                        'num' => array('name' => 'num', 'value' => '$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)', 'htmlOptions' => array('width' => '3%'), 'filter' => false),
                        array('type' => 'raw', 'name' => 'date_from',
                            'value' => '(!Yii::app()->controller->checkAccess(\'manage\'))?$data->getDatesTitle():"<a href=\'".Yii::app()->createUrl("/salary/default/reportUpdate", array("id" => $data->salary_report_id))."\' class=\'".($data->status == SalaryReport::STATUS_CLOSED ? "closed-report" : "")."\'>".$data->getDatesTitle()."</a>"',
                            'filter' => BSHtml::activeTextField($model, 'date_from', ['class' => 'daterange form-control']),
                            'htmlOptions' => array('width' => '25%')
                        ),
                        array('type' => 'raw', 'name' => 'date_created',
                            'value' => '$data->getDateCreated()',
                            'filter' => false,
                            'htmlOptions' => array('width' => '12%')
                        ),

                        array('type' => 'raw', 'name' => 'user_id',
                            'value' => '$data->getName()',
                            'filter' => $this->checkAccess('manage') ? BSHtml::activeDropDownList($model, 'user_id', User::model()->getDropDown(), ['empty' => '', 'class' => 'selectbox form-control']) : false,
                            'htmlOptions' => array('width' => '20%')
                        ),
                        array('type' => 'raw', 'name' => 'value',
                            'value' => '$data->getValue()',
                            'filter' => '',
                            'htmlOptions' => array('width' => '15%')
                        ),
                        array('type' => 'raw', 'name' => 'paid_value',
                            'value' => '$data->getPaidValue()',
                            'filter' => '',
                            'htmlOptions' => array('width' => '15%')
                        ),

                        array('type' => 'raw', 'name' => 'status',
                            'value' => '"<span class=\'status-".$data->status."\'>" . $data->getStatusTitle($data->status) . "</span>"',
                            'filter' => BSHtml::activeDropDownList($model, 'status', SalaryReport::getStatusList(true),['class' => 'form-control selectbox', 'empty' => '']),
                            'htmlOptions' => array('width' => '10%')
                        ),

                        array(
                            'type' => 'raw', 'name' => '', 'htmlOptions' => array('width' => '15%', 'class' => 'text-right', 'style' => 'white-space: nowrap;'), 'header' => '', 'filter' => false, 'sortable' => false,
                            'value' => '
                         (($data->status == SalaryReport::STATUS_NEW && $this->grid->controller->checkAccess("manage")) ? "<a class=\'has-tooltip need-confirm btn btn-xs btn-default\' style=\'margin-right: 5px\' title=\'Закрыть расчет\' data-msg=\'Вы уверены, что хотите закрыть данный расчет?\' href=\'".Yii::app()->createUrl("salary/default/reportclose/id/".$data->salary_report_id)."\'><span class=\'ti-check\'></span></a>" : "")  .
                         (($data->status == SalaryReport::STATUS_NEW && $this->grid->controller->checkAccess("manage") && $data->checkLimit()) ? "<a class=\'has-tooltip btn btn-xs btn-default show-pay-form\' data-id=\'".$data->salary_report_id."\' style=\'margin-right: 5px\' title=\'Выплатить расчет\' href=\'#\'><span class=\'glyphicon glyphicon-rub\'></span></a>" : "") .
                          "<span class=\'has-tooltip show-report-details pointer\' data-id=\'".$data->salary_report_id."\' style=\'margin-left: 10px; margin-right: 10px;\' title=\'Детали расчета\' ><span class=\'ti-receipt\'></span></span>" .
                          "<a class=\'has-tooltip export-excel no-loader \' data-id=\'".$data->salary_report_id."\' style=\'margin-right: 5px\' title=\'Загрузить в excel\' href=\'".Yii::app()->createUrl("/salary/api/getReportExcel/id/" . $data->salary_report_id)."\'><span class=\'ti-download\'></span></a>" .
                          
                         (($this->grid->controller->checkAccess("delete") && $this->grid->controller->checkAccess("manage")) ? "<a class=\'has-tooltip to-trash\' style=\'color: #777; margin-left: 10px\' title=\'В корзину\' href=\'".Yii::app()->createUrl("/salary/default/totrash/id/".$data->salary_report_id)."\'><span class=\'ti-trash\'></span></a>" 
                         : "")'
                        ),
                    ),
                    'enableSorting' => true,
                    'emptyText' => 'Зарплаты не найдены',
                    'ajaxUrl' => Yii::app()->createUrl('salary/default/index')
                )
            );
            ?>
        </div>
    </div>

<? $this->scripts($this->module->assetsUrl . '/js/payReport.js', true) ?>
<?$this->renderPartial('_details')?>
<?$this->renderPartial('_pay_form')?>
