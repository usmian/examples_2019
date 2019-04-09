<div class="modal modal-pay-salary" id="modal-pay-salary">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4>Выплата расчета</h4></div>
            <div class="modal-body no-labels-margin" style="min-height: 100px">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success pay-salary">Сохранить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<script type="text/template" id="tpl-pay-salary">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Период:</label>
            </div>
        </div>
        <div class="col-md-6"><%=dates%></div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Сотрудник:</label>
            </div>
        </div>
        <div class="col-md-6"><%=user%></div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Общая сумма:</label>
            </div>
        </div>
        <div class="col-md-6 limit-text"><%=app.formatPrice(value)%></div>
    </div>
    <% if(limit>0){ %>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Остаток выплаты:</label>
            </div>
        </div>
        <div class="col-md-6 report-limit report-sum" data-value="<%=limit%>"><%=app.formatPrice(limit)%></div>
    </div>
    <% } %>
    <hr/>
    <% if(costs.length){ %>
    <table class="table hover">
        <thead>
        <tr>
            <th>Дата</th>
            <th>Сумма</th>
            <th>Тип</th>
        </tr>
        </thead>
        <tbody>
        <% _.each(costs, function(cost, i) { %>
        <tr class="costs cost-item">
            <td width="30%"><%=cost.date_cost%></td>
            <td width="35%"><%=cost.value%></td>
            <td width="35%"><%=cost.type%></td>
        </tr>
        <% }) %>
        </tbody>
    </table>
    <% if(limit>0){ %>
    <hr/>
         <% } %>
    <% } %>
    <% if(limit>0){ %>

    <div class="row">
        <div class="col-md-4">
            <label>Дата:</label>
            <div><input type="text" value="<%=date%>" class="form-control payment-date"/></div>
        </div>
        <div class="col-md-3">
            <label>Сумма:</label>
            <input type="text" class="form-control payment-sum"/>
        </div>
        <div class="col-md-5">
            <label>Тип:</label>
            <select class="form-control selectbox payment-type">
                <option value=""></option>
                <option value="<?= Cost::TYPE_CASH ?>"><?= Cost::model()->getType(Cost::TYPE_CASH); ?></option>
                <option value="<?= Cost::TYPE_CARD ?>"><?= Cost::model()->getType(Cost::TYPE_CARD); ?></option>
            </select>
        </div>
    </div>
    <%}%>
    <input type="text" value="<%=paid%>" class="paid-value" hidden/>
</script>

<script src="<?= $this->module->assetsUrl ?>/js/salaryPay.js"></script>

