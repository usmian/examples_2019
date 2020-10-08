<?php
/** @var BsActiveForm $form */
/** @var $model SalaryReport */
$titles = array('create' => 'Новый расчет', 'update' => 'Редактирование расчета');
$this->pageTitle = $titles[$action] . ' - Зарплаты - ' . Yii::app()->name;

$this->breadcrumb(array('Зарплаты' => '/salary', $titles[$action]));

$disabled = $model->status > SalaryReport::STATUS_NEW ? 'disabled' : false;
?>


<? if ($action === 'update'): ?>
    <?
    $this->submenu(array(
        array(
            'link' => 'salary/default/index',
            'title' => 'Расчеты', 'icon' => 'ti-angle-left'
        ),
        array(
            'link' => 'salary/default/reportUpdate/id/' . $model->salary_report_id,
            'title' => 'Редактировать расчет', 'icon' => ''
        ),
        array(
            'link' => 'salary/default/totrash/id/' . $model->salary_report_id, 'linkClass' => 'to-trash', 'class' => 'to-trash-btn',
            'title' => 'В корзину', 'icon' => 'ti-trash', 'visible' => $this->checkAccess('delete') && !$model->in_trash
        ),
        array(
            'link' => 'salary/default/fromtrash/id/' . $model->salary_report_id, 'linkClass' => 'from-trash', 'class' => 'to-trash-btn',
            'title' => 'Восстановить из корзины', 'icon' => 'ti-back-left', 'visible' => $this->checkAccess('delete') && $model->in_trash
        ),

    ));
    ?>
<? else: ?>
    <?
    $this->submenu(array(
        array(
            'link' => 'salary/default/index',
            'title' => 'Расчеты', 'icon' => 'ti-angle-left'
        ),
        array(
            'link' => 'salary/default/reportCreate/',
            'title' => 'Новый расчет', 'icon' => ''
        ),
    ));
    ?>
<? endif; ?>


<?php $form = $this->beginWidget('bootstrap.widgets.BsActiveForm', array(
    'id' => 'createUpdate',
    'enableAjaxValidation' => false,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
    ),
    'htmlOptions' => array('class' => ''),
)); ?>


<div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6">
                Информация о расчете
            </div>
            <div class="col-md-6">
                <?php if ($action == 'update') : ?>
                    <div class="text-right">
                        <? if ($model->reporter_id) { ?>
                            <div class="document-info-block">
                                <strong>Автор: </strong> <?= Helper::getShortName(UserController::model()->getNameById($model->reporter_id)) ?>
                            </div>
                        <? } ?>
                        <? if ($model->date_created) { ?>
                            <div class="document-info-block">
                                <strong>Добавлено: </strong> <?= Helper::getBeautyDateTime($model->date_created) ?>
                            </div>
                        <? } ?>
                        <? if ($model->date_updated) { ?>
                            <div class="document-info-block">
                                <strong>Обновлено: </strong> <?= Helper::getBeautyDateTime($model->date_updated) ?>
                            </div>
                        <? } ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-3">
                <? if ($action == 'create') { ?>
                    <div class="">
                        <?php echo $form->dropDownListControlGroup($model, 'period', SalaryReport::getDatesList(), ['class' => 'selectbox dates']); ?>
                    </div>
                <? } else { ?>
                    <div class="form-group">
                        <label>Период</label>
                        <span class="salary-title font-lg"><?= $model->getDatesTitle() ?></span>
                    </div>
                <? } ?>
            </div>
            <div class="col-md-3 custom-dates-wrap" <? if ($model->period != 'custom'){ ?>style="display: none"<? } ?>>
                <label>&nbsp;</label>
                <div class="no-label">
                    <?php echo $form->textFieldControlGroup($model, 'custom_dates', ['class' => 'daterange custom-date', 'autocomplete'=>"off"]); ?>
                </div>
            </div>
            <div class="col-md-3 form-parent">
                <? if ($action == 'create') { ?>
                    <?php echo $form->dropDownListControlGroup($model, 'user_id', UserController::model()->getDropDownWithRoles(), ['class' => 'selectbox user', 'empty' => '']); ?>
                <? } else { ?>
                    <div class="form-group">
                        <label>Сотрудник</label>
                        <span class="salary-title font-lg"><?= $model->getUserName(true) ?></span>
                    </div>
                <? } ?>

            </div>
            <div class="col-md-2">
                <div class="">
                    <?php echo $form->textFieldControlGroup($model, 'salary_value', ['class' => 'filter-amount salary-value value', 'placeholder' => ' ', 'disabled' => $disabled]); ?>
                </div>
            </div>
        </div>

    </div>
</div>


<div class="panel panel-default">
    <div class="panel-heading">Бонусы и штрафы</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-2">
                <div class="">
                    <?php echo $form->textFieldControlGroup($model, 'bonus_value', ['class' => 'filter-amount bonus-value value', 'placeholder' => ' ', 'disabled' => $disabled]); ?>
                </div>
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                <div class="no-label">
                    <?php echo $form->textFieldControlGroup($model, 'bonus_comment', ['class' => '', 'disabled' => $disabled]); ?>
                </div>
            </div>
            <div class="col-md-2">
                <div class="">
                    <?php echo $form->textFieldControlGroup($model, 'penalty_value', ['class' => 'filter-amount penalty-value value', 'placeholder' => ' ', 'disabled' => $disabled]); ?>
                </div>
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                <div class="no-label">
                    <?php echo $form->textFieldControlGroup($model, 'penalty_comment', ['class' => '', 'disabled' => $disabled]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<? if ($action == 'update') : ?>
    <div class="panel panel-default comissions">
        <div class="panel-heading">Комисcия за услуги</div>
        <div class="panel-body ">
            <div class="no-service" style="text-align:center;display: none">Услуги не найдены</div>
            <div id="table-services-wrap" class="doctors-table" style="display: none"></div>
            <div class="form-group services-wrap-sum" style="display: none">
            <hr/>
                <label></label><strong>Общая сумма комиссии: </strong><span class="services-sum font-lg"> <?= Helper::price($model->services_value)?></span>
            </div>
        </div>
    </div>

    <div class="panel panel-default costs-panel">
        <div class="panel-heading clearfix">Выплаты
            <? if ($model->status == SalaryReport::STATUS_NEW && $this->checkAccess("list", "payment") && $model->checkLimit()) { ?>
                <a class="btn btn-default pull-right show-pay-form btn-xs"
                   data-id="<?= $model->salary_report_id ?>">
                    <span class="glyphicon glyphicon-rub" style="color: black"></span>Выплатить расчет</a>
            <?}?>
        </div>
        <div class="panel-body">
            <div class="no-costs" style="text-align:center;display: none">Нет выплат</div>
            <div id="table-costs-wrap" class="costs-table" style="display: none"></div>
        </div>
    </div>

<? endif; ?>


<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="form-group">
                <? if ($action == 'create') { ?><div class="col-md-12"><?}?>
                    <? if ($action == 'update') { ?><div class="col-md-4"><?}?>
                        <label class="inline">Общая сумма:</label>
                        <span class="salary-result-value salary-title font-lg" style="margin-left: 7px;"></span>
                        <? if ($action == 'update') { ?>
                    </div>
                    <div class="col-md-4 paid-text">
                        <label class="inline">Выплачено:</label>
                        <span class="paid-value salary-title font-lg" style="margin-left: 7px;"></span>
                    </div>
                    <div class="col-md-4 buttons-block">
                        <? if ($model->status == SalaryReport::STATUS_NEW && $this->checkAccess("list", "payment")) { ?>
                            <a class='btn btn-default position-right need-confirm btn-sm' data-msg="Вы уверены, что хотите закрыть данный расчет?"
                               href="<?= Yii::app()->createUrl("salary/default/reportclose/id/" . $model->salary_report_id) ?>"><span
                                        class='ti-check'></span>Закрыть расчет</a>
                        <? } else { ?>
                            <div class="text-right">
                                <label class="inline">Статус:</label> <?= $model->getStatusTitle($model->status) ?>
                            </div>
                        <? } ?>
                        <? } ?>
                    </div>
                </div>
            </div>
            <? if ($action == 'create') { ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="no-label">
                            <?php echo $form->textAreaControlGroup($model, 'comment', ['placeholder' => 'Комментарий', 'rows' => 3]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <? } else { ?>
</div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Комментарии</div>
        <div class="panel-body">
            <? $this->renderPartial('//layouts/catalogs/comments', array(
                'model' => 'SalaryReport', 'id' => $model->salary_report_id, 'controller' => 'salary/api',
                'noPanel' => true
            )); ?>
        </div>
    </div>

<? } ?>

<div class="panel panel-buttons">
    <div class="panel-body">
        <div class="submit">
            <? if (!$model->status || $model->status == SalaryReport::STATUS_NEW) { ?>
                <button class="btn btn-success btn-save">Сохранить</button>
            <? } ?>
            <a class="btn btn-default " href="<?= Yii::app()->createUrl('salary/default/index') ?>">Отмена</a>
        </div>
    </div>
</div>
</div>
<input type="hidden" id="users-salary" value='<?= json_encode(UserSalary::getAllValues()); ?>'/>
<input type="hidden" id="users-doctors" value='<?= json_encode(UserController::getIdsByRole('doctor')); ?>'/>

<? if ($action == 'update'): ?>
    <?= $form->hiddenField($model, 'period'); ?>
<? endif; ?>

<input type="hidden" class="action" value="<?= $action ?>"/>
<input type="hidden" class="salary-id" value="<?= $model->salary_report_id ?>"/>
<input type="hidden" class="services-value" value="<?= $model->services_value ?>"/>
<input type="hidden" class="paid-hidden" value="<?= $model->paid_value ?>"/>

<?php $this->endWidget(); ?>

<? $this->scripts($this->module->assetsUrl . '/js/reportsSalary.js', true) ?>
<? $this->scripts($this->module->assetsUrl . '/js/payReport.js', true) ?>
<? $this->renderPartial('_pay_form') ?>

<script type="text/template" id="tpl-report-table">
    <table class="table table-hover table-services-form">
        <thead>
        <tr>
            <th width="5%"></th>
            <th width="5%">Дата</th>
            <th width="20%">Пациент</th>
            <th width="25%">Услуга</th>
            <th width="5%">Кол-во</th>
            <th width="10%">Стоимость</th>
            <th width="10%">Комиссия</th>
            <th width="10%">Тип</th>
            <th width="5%"></th>
        </tr>
        </thead>
        <tbody>
        <% _.each(data, function(item, i) { %>
        <tr>
            <td width="5%"><a target="_blank" href=""></a>
                <%=i+1%>
            </td>
            <td width="15%"><%=item.date_service%></td>
            <td width="20%"><%=item.patient_name%></td>
            <td width="25%"><%=item.code + ' ' + item.service_name%></td>
            <td width="5%" style="text-align: center"><%=item.count%></td>
            <td width="10%"><%=item.original_value%></td>
            <td width="10%"><%=item.result_value%></td>
            <td width="10%"><%=item.type_name%></td>

            <td class="text-right" width="5%">
                <?php if ($model->status != SalaryReport::STATUS_CLOSED) : ?>
                    <span data-report-id="<%= item.salary_report_id %>"
                          data-service-id="<%= item.salary_report_service_id %>"
                          title="Удалить" class="has-tooltip ti-trash service-delete pointer">
                    </span>
                <?php endif; ?>
            </td>
        </tr>
        <% }) %>
        </tbody>
    </table>
</script>

<script type="text/template" id="tpl-costs-table">
    <table class="table table-hover table-costs-form">
        <thead>
        <tr>
            <th width="25%">Дата</th>
            <th width="25%">Сумма</th>
            <th width="45%">Тип выплаты</th>
            <th width="5%"></th>

        </tr>
        </thead>
        <tbody>
        <% _.each(data, function(item, i) { %>
        <tr>
            <td class=""><%=item.date_cost%></td>
            <td class=""><%=item.value%></td>
            <td class=""><%=item.type%></td>
            <td class="text-right">
                <?php if ($model->status != SalaryReport::STATUS_CLOSED) : ?>
                    <span data-cost-id="<%= item.cost_id %>" data-report-id="<%= item.report_id %>"
                          title="Удалить" class="has-tooltip ti-trash cost-delete pointer">
                    </span>
                <?php endif; ?>
            </td>
        </tr>
        <% }) %>
        </tbody>
    </table>
</script>
