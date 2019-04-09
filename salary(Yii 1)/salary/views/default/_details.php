<div class="modal modal-detailing" id="modal-detailing">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 style="display: inline-block">Детализация расчета</h4>
            </div>
            <div class="modal-body">
                <div class="body-wrap no-labels-margin">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<script type="text/template" id="tpl-salary-table">
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Период:</label></div>
                </div>
                <div class="col-md-6"><%= report.period %></div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Сотрудник:</label></div>
                </div>
                <div class="col-md-6"><%= report.user_name %></div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Итоговая сумма:</label></div>
                </div>
                <div class="col-md-6 sum-value"><strong><%=report.value%></strong></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Статус:</label></div>
                </div>
                <div class="col-md-6"><%= report.status %></div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Добавлено:</label></div>
                </div>
                <div class="col-md-6"><%= report.create_report %></div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Обновлено:</label></div>
                </div>
                <div class="col-md-6"><%= report.update_report %></div>
            </div>
        </div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group"><label>Оклад:</label></div>
        </div>
        <div class="col-md-3"><%=report.salary%></div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group"><label>Бонус:</label></div>
        </div>
        <div class="col-md-3"><%if (report.bonus_value){%><span class="green">+<%= report.bonus_value %><%}%></span></div>
        <div class="col-md-6"><span class="note"><%= report.bonus_comment %></span></div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group"><label>Штраф:</label></div>
        </div>
        <div class="col-md-3"><%if (report.penalty_value){%><span class="red">-<%=report.penalty_value%><%}%></span></div>
        <div class="col-md-6"><span class="note"><%= report.penalty_comment %></span></div>
    </div>

    <% if (services.length) { %>
    <hr/>
    <h5>Комиссия за оказанные услуги</h5>
    <div class="no-search no-length">
    <table class="dynamics-table display compact table services-table table-xs">
        <thead>
        <tr>
            <th width="5%"></th>
            <th width="10%">Дата</th>
            <th width="15%">Пациент</th>
            <th width="25%">Услуга</th>
            <th width="10%">Кол-во</th>
            <th width="10%">Ст-ть</th>
            <th width="10%">Комиссия</th>
            <th width="20%">Тип</th>
        </tr>
        </thead>
        <tbody>
        <% _.each(services, function(k, i) { %>
        <tr>
            <td><a target="_blank" href=""></a>
                <%=i+1%>
            </td>
            <td><%=k.date_service%></td>
            <td class="total-price"><%=k.patient_name%></td>
            <td><%=k.code+' '+k.service_name%></td>
            <td style="text-align: center"><%=k.count%></td>
            <td><%=k.original_value%></td>
            <td><%=k.result_value%></td>
            <td><%=k.type_name%></td>
        </tr>
           <% }) %>
        </tbody>
    </table>
        <div style="margin-top: 15px;">
            <div class="row">
                <div class="col-md-3">
                    <label class="inline">Общая сумма комиссии: </label>
                </div>
                <div class="col-md-3">
                    <span><%=sum%></span>
                </div>
            </div>
        </div>
    </div>
    <% } %>

    <hr/>
    <h5>Выплаты</h5>
    <% if (costs.length) { %>
        <table class="dynamics-table display compact table main-table table-xs table-hover">
            <tbody>
            <% _.each(costs, function(c, i) { %>
            <tr>
                <td class="col-md-3"><%=c.date_cost%></td>
                <td class="col-md-3"><%=c.value%></td>
                <td class="col-md-6"><%=c.type%></td>
            </tr>
            <% }) %>
            </tbody>
        </table>
    <% } %>
    <div class="row">
        <div class="col-md-3">
            <label class="inline">Всего выплачено: </label>
        </div>
        <div class="col-md-3">
            <span><%=report.paid_value%></span>
        </div>
    </div>

    <hr/>
        <h5>Комментарии</h5>
        <? $this->renderPartial('//layouts/catalogs/comments', array(
                'model' => 'SalaryReport', 'id' => '', 'controller' => 'salary/api',
                'noPanel' => true, 'init' => false, 'template' => false
            )
        );?>
</script>

<? $this->renderPartial('//layouts/catalogs/commentsTemplates');?>