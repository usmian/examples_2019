<?php
$this->pageTitle = 'Выгрузка по услугам - Зарплаты - ' . Yii::app()->name;
$this->breadcrumb(array('Зарплаты' => '/salary', 'Выгрузка по услугам'));
?>

<? if ($this->checkAccess('manage')) {?>
    <? $this->renderPartial('_menu'); ?>
<?}?>

<div class="panel panel-default">
    <div class="panel-heading">
        <span class="ti-filter"></span> Фильтр
    </div>
    <div class="panel-body">
        <div class="form-group filter-block">
            <div class="row">
                <div class="col-md-2">
                    <select class="period selectbox form-control" data-no_search="1">
                        <? $default = 'month' ;?>
                        <? foreach (StatHelper::getPeriods() as $k => $item):?>
                            <option value="<?=$k?>" <?if($k == $default){?>selected<?}?> ><?=$item['title']?></option>
                        <? endforeach; ?>
                        <option value="custom">Выбрать интервал</option>
                    </select>
                </div>
                <div class="col-md-2 from-wrap" style="display: none;">
                    <div class="relative">
                        <input type="text" class="dates date-range form-control"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <? $this->renderPartial('//layouts/include/_clinics', [
                        'class' => 'form-control clinic', 'empty' => false, 'selected' => Clinic::model()->current_id,
                        'attrs' => "multiple='multiple' data-placeholder='[Клиники]'"
                    ]) ?>
                </div>
                <div class="col-md-2">
                    <select class="doctor selectbox form-control">
                        <option value="">[Сотрудник]</option>
                        <? foreach (UserController::model()->getDropDown(Role::ROLE_DOCTOR) as $k => $item):?>
                            <option value="<?=$k?>"><?=$item?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="professions selectbox form-control" data-placeholder="[Специальности]" multiple="multiple">
                        <? foreach (Profession::model()->getList(true) as $k => $item):?>
                            <option value="<?=$k?>"><?=$item?></option>
                        <? endforeach; ?>
                    </select>
                </div>

            </div>
        </div>

        <div class="form-group filter-block">
            <div class="row">
                <div class="col-md-4">
                    <select class="service selectbox no-selectbox form-control"></select>
                </div>
                <div class="col-md-2">
                    <select class="company selectbox form-control">
                        <option value="">[Тип счета]</option>
                        <option value="1">Физ. лицо</option>
                        <option value="2">Юр. компания</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="payment selectbox form-control">
                        <option value="">[Тип оплаты]</option>
                        <? foreach (PatientPayment::model()->getIncomeTypeList(true) as $i => $title) {?>
                            <option value="<?=$i?>"><?=$title?></option>
                        <?}?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <select class="patient-type selectbox form-control">
                        <option value="">[Тип пациента]</option>
                        <?php foreach (Patient::model()->getLabelType() as $i => $title) : ?>
                            <option value="<?=$i?>"><?=$title?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="recommendation selectbox form-control">
                        <option value="">[Рекомендация]</option>
                        <? foreach (UserController::model()->getDropDown() as $k => $item):?>
                            <option value="<?=$k?>"><?=$item?></option>
                        <? endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="btn btn-default btn-show"><span class="ti-search"></span> Показать</div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6">
                <span class="ti-list"></span> Список услуг
            </div>
            <div class="col-md-6 text-right">
                <div class="btn btn-sm download btn-default"><span class="ti-download"></span> Загрузить CSV</div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="datatable-wrap no-search">
            <div class="patients-columns"></div>
            <table class="table patients table-hover display nowrap table-xs">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>Врач</th>
                    <th>Клиника</th>
                    <th>№ счета</th>
                    <th>Тип счета</th>
                    <th>Код услуги</th>
                    <th>Услуга</th>
                    <th>Стоимость</th>
                    <th>Кол-во</th>
                    <th>Скидка</th>
                    <th>Итого</th>
                    <th>Пациент</th>
                    <th>Тип пациента</th>
                    <th>Тип оплаты</th>
                    <th>Дата оплаты</th>
                    <th>Долг</th>
                    <th>Рекомендация</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<? $this->scripts($this->module->assetsUrl . '/js/services.js', true) ?>
