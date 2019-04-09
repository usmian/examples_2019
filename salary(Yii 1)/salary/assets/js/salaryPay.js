var salaryPay = (function () {

    var id, modal, wrap, value, limitValue;

    /**
     *
     */
    function init() {
        bind();
        modal = $('.modal-pay-salary');
        wrap = $('.modal-body', modal);

    }

    /**
     *
     */
    function bind() {
        $(document).on('click', '.show-pay-form', function () {
            id = $(this).data('id');
            getData(true);
        }).on('click', '.report-limit', function () {
            calculate($('.payment-sum', modal), $('.report-limit').data('value'));
        });
        $('.pay-salary').click(function () {
            save();
        });
    }

    /**
     * @param show
     */
    function getData(show) {
        wrap.empty();
        if(show){
            modal.modal();
        }
        if(!show){
            $('.show-pay-form').hide();
        }
        app.get({url: '/salary/api/', method: 'detail'}, {id: id}, function (r) {
            if (r.error || !r.data) {
                return;
            }
            drawForm(r.data);
        }, wrap);

    }

    /**
     * @param data
     */
    function drawForm(data) {
        value = data.value;
        limitValue = data.limit;
        limitValue = parseFloat(limitValue.toFixed(3));
        var html = $(_.template($('#tpl-pay-salary').html(), data)(data));
        wrap.append(html);
        var selectWrap = $('.payment-type', modal),
            dateWrap = $('.payment-date', modal),
            paymentWrap = $('.payment-sum', modal);

        forms.selectbox(selectWrap, {disable_search: true});
        forms.datepicker(dateWrap);
        forms.filterAmount(paymentWrap);

        paymentWrap.change(function () {
            calculate($(this));
        });
    }

    /**
     *@param elem
     * @param all
     */
    function calculate(elem, all) {
        var sumValue = app.clearPrice(elem.val()),
            newValue,
            limit = $('.report-limit'),
            reportsLimit = limit.data('value');

        newValue = reportsLimit - sumValue;
        if (all > 0) {
            limit.html(app.formatPrice(0));
            forms.filterAmount(elem.val(all));
            limit.trigger('blur');
            elem.trigger('blur');
        } else {
            if (newValue < 0) {
                return;
            }
            limit.html(app.formatPrice(newValue));
            limit.trigger('blur');
        }
    }

    /**
     *
     */
    function validate() {
        $('.has-error', modal).removeClass('has-error');
        var valuePayment = $('.payment-sum', modal).val(),
            paymentType = $('.payment-type', modal).val(),
            paymentDate = $('.payment-date', modal).val();

        if (!valuePayment || (valuePayment > limitValue) || !paymentType || !paymentDate) {
            $('.payment-sum').addClass('has-error');
            return false;
        }
        return true;
    }

    /**
     *
     */
    function save() {
        if (!validate()) {
            return false;
        }
        modal.hide();
        app.confirm('Вы уверены, что хотите добавить выплату по расчету?', function () {
            var limit = $('.report-limit', modal).data('value'),
                paid = app.clearPrice($('.payment-sum', modal).val()),
                sum = limit - paid;

            app.post({url: '/salary/api/', method: 'pay'}, {
                id: id,
                sum: sum,
                paid: paid,
                type: $('.payment-type', modal).val(),
                date: $('.payment-date', modal).val()
            }, function (r) {
                if (r.error || !r.data.save) {
                    app.error('Ошибка выплаты расчета');
                    return;
                }
                modal.modal('hide');
                var grid = document.getElementById('grid-reports');
                if (grid !== null) {
                    forms.refreshGrid($('#grid-reports'));
                } else {
                    $('.paid-hidden').val(r.data.paid);
                    reportsSalary.getCosts(id);
                    reportsSalary.calculate();
                }

                getData(r.data.modal);
                app.message('Расчет выплачен');
            }, wrap);
        }, function () {
            modal.show();
        });
    }

    //
    $(document).ready(function () {
        init();
    });

})();