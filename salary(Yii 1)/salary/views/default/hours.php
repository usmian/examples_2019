<?php
$this->pageTitle = 'Выгрузка по часам - Зарплаты - ' . Yii::app()->name;
$this->breadcrumb(array('Зарплаты' => '/salary', 'Выгрузка по часам'));
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
                    <? $this->renderPartial('//layouts/include/_clinics', ['class' => 'form-control clinic', 'empty' => false, 'attrs' => "multiple='multiple' data-placeholder='[Клиники]'"]) ?>
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

        <div class="filter-block form-group">
            <div class="row">
                <div class="col-md-2">
                    <select class="type selectbox form-control">
                        <option value="">[Тип подсчета]</option>
                        <option value="1">Только по расписанию</option>
                        <option value="2">Только по визитам</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="btn btn-default btn-show"><span class="ti-search"></span> Показать</div>
                </div>
            </div>
        </div>


    </div>
</div>

<div class="panel panel-default list-wrap">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6">
                <span class="ti-list"></span> Список сотрудников
            </div>
            <div class="col-md-6 text-right">
                <div class="btn btn-sm download btn-default"><span class="ti-download"></span> Загрузить CSV</div>
            </div>
        </div>
    </div>
    <div class="panel-body hours-wrap">

    </div>
</div>

<script type="text/template" id="tpl-hours-table">
    <%if (data.length) {%>
    <div class="no-search no-length no-export">
        <div class="patients-columns"></div>

        <div class="callout callout-info callout-sm">
            <%if (!type) {%>
                <strong>P</strong> - время работы сотрудника на основе расписания в часах,
                <strong>В</strong> - время работы сотрудника на основе визитов в часах,
                <strong>%</strong> - отношение времени работы по визитам к времени работы по расписанию
            <%}else{%>
                Время работы сотрудников на основе <%=(type == 1 ? "расписания" : "визитов")%> в часах.
            <%}%>
        </div>

        <table class="table hours table-hover display nowrap table-xs">
            <thead>
            <tr>
                <th <%if (!type){%>rowspan="2"<%}%> class="border-right" style="background: white">Сотрудник</th>

                <th <%if (!type){%> colspan="3"<%}%> class="border-right text-center" style="background: white">Всего</th>

                <% _.each(data[0].hours, function(data, date) { %>
                    <th <%if (!type){%>colspan="3"<%}%> class="text-center"><%=datetime.convertDateBack(date, true)%></th>
                <%})%>
            </tr>

            <%if (!type){%>
            <tr>
                <th>Р</th><th>В</th><th class="border-right">%</th>
                <% _.each(data[0].hours, function(data, date) { %>
                    <th>Р</th><th>В</th><th class="border-right">%</th>
                <%})%>
            </tr>
            <%}%>
            </thead>

            <tbody>
            <% _.each(data, function(i) {%>
            <tr>
                <td class="border-right"><%=i.name%></td>

                <%if (!type || type == 1){%>
                    <td <%if (type){%>class="text-center"<%}%>><%=i.total.schedule%></td>
                <%}%>
                <%if (!type || type == 2){%>
                    <td <%if (type){%>class="text-center"<%}%>><strong><%=i.total.appointments%></strong></td>
                <%}%>
                <%if (!type){%>
                    <td class="border-right muted"><%=i.total.ratio%></td>
                <%}%>

                <% _.each(i.hours, function(d, date) { %>
                    <%if (!type || type == 1){%>
                        <td <%if (type){%>class="text-center"<%}%>><%=d.schedule%></td>
                    <%}%>
                    <%if (!type || type == 2){%>
                        <td <%if (type){%>class="text-center"<%}%>><strong><%=d.appointments%></strong></td>
                    <%}%>
                    <%if (!type){%>
                        <td class="border-right muted"><%=d.ratio%></td>
                    <%}%>
                <%})%>
            </tr>
            <%})%>
            </tbody>

        </table>

    </div>

    <%}else{%>
        <p>Данные не найдены</p>
    <%}%>
</script>

<? $this->scripts($this->module->assetsUrl . '/js/hours.js', true) ?>
