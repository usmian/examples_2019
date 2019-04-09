var reportsSalary = (function () {
    var dates = $('.dates');

    /**
     *
     */
    function init() {
        reportsSalary.apiUrl = app.baseUrl + '/salary/api/';
        if ($('.action').val() === 'create') {
            changeForUser($('.user'));
        }
        if ($('.doctors-table').length) {
            var id = $('.salary-id').val();
            getServices(id);
            getCosts(id);
        }

        bind();
    }

    /**
     *
     */
    function bind() {
        var user = $('.user');
        app.currencyShort ='руб.';
        forms.selectbox(dates, {disable_search: true});
        forms.selectbox(user);
        forms.selectbox($('.status'));
        bindDateRange();
        handleDates();
        var paid = $('.paid-hidden').val();
        $('.paid-value').html(app.formatPrice(paid));
        $(document).on('click', '.service-delete', function () {
            if ($('.action').val() == 'update') {
                getDelete($(this));
                return;
            }
        });

        $(document).on('click', '.cost-delete', function () {
                deleteCost($(this));
        });
        dates.change(function () {
            change(changeForDate, $(this));
        });

        user.change(function () {
            change(changeForUser, $(this));
        });
        if ($('.action').val() === 'update') {
            calculate();
        }
        $('.value').bind('keyup blur', function () {
            calculate();
        });
    }
    /**
     * @param idReportServices
     */
    function getCosts(idReportServices){
        app.get({url: reportsSalary.apiUrl, method: 'getCosts'}, {id: idReportServices}, function (r) {
            if (r.data.length) {
                var html = $(_.template($('#tpl-costs-table').html(), r)(r));
                $('.costs-table').html(html);
                $('.no-costs').hide();
                $('.costs-table').show();
                //forms.dataTable($('.table-services-form'), {"tableTools": {"aButtons":[]}});
                app.tooltips();
            } else {
                $('.costs-table').hide();
                $('.no-costs').show();

            }
            calculate();
        }, $('.costs-panel'));
    }
    /**
     * @param elem
     */
    function deleteCost(elem){
        app.confirm('Вы уверены, что хотите эту выплату из расчета?', function () {
            var data = {
                id: elem.data('cost-id'),
                report_id: elem.data('report-id')
            };
            app.post({url: reportsSalary.apiUrl, method: 'deleteCost'}, {data: data}, function (r) {
                if (r.error) {
                    return false;
                }
                $('.paid-hidden').val(r.data.value);
                $('.paid-value').html(app.formatPrice(r.data.value));
                getCosts(r.data.report_id);
                if ($('.show-pay-form').is(':hidden'))
                {
                    $('.show-pay-form').show();
                }
            }, false);
        }, function () {
        });
    }
    /**
     *
     * @param elem
     */
    function getDelete(elem) {
        app.confirm('Вы уверены, что хотите эту услугу из расчета?', function () {
            var data = {
                id: elem.data('service-id'),
                report_id: elem.data('report-id')
            };
            app.post({url: reportsSalary.apiUrl, method: 'deleteService'}, {data: data}, function (r) {
                if (r.error) {
                    return false;
                }
                $('.services-value').val(r.data.value);
                $('.services-sum').html(app.formatPrice(r.data.value));
                getServices(r.data.report_id);

            }, false);
        }, function () {
        });
    }

    /**
     *
     */
    function calculate() {
        var salary = app.clearPrice($('.salary-value').val()),
            bonus = app.clearPrice($('.bonus-value').val()),
            penalty = app.clearPrice($('.penalty-value').val()),
            services_prices = app.clearPrice($('.services-value').val()),
            paid = $('.paid-hidden').val(),
            resultValue = (salary + bonus - penalty + services_prices);

        $('.salary-result-value').html(app.formatPrice(resultValue));
        $('.paid-value').html(app.formatPrice(paid));
    }

    /**
     *
     * @param elem
     */
    function changeForUser(elem) {

        if (!$(elem).val()) {
            $('.salary-value').val('').trigger('blur');
            return;
        }
        var id = $(elem).val(),
            value = $('#users-salary').val(),
            doctorIds = $('#users-doctors').val();

        if (id.length) {
            $('.salary-value').val($.parseJSON(value)[id]).trigger('blur');
            if (doctorIds.indexOf(id) != -1) {
                $('.btn-save').html('Продолжить');
            } else {
                $('.btn-save').html('Сохранить');
            }
        }
    }

    /**
     *
     * @param elem
     */
    function changeForDate(elem) {
        handleDates();
        if ($(elem).val() == 'custom' || !$('.user').val()) {
            return;
        }
    }

    /**
     * @param idReportServices
     */
    function getServices(idReportServices) {
        app.get({url: reportsSalary.apiUrl, method: 'getServices'}, {id: idReportServices}, function (r) {
            if (r.error) {
                return;
            }
            if (r.data.length) {
                var html = $(_.template($('#tpl-report-table').html(), r)(r));
                $('.doctors-table').html(html);
                $('.no-service').hide();
                $('.doctors-table').show();
                forms.dataTable($('.table-services-form'), {"tableTools": {"aButtons":[]}});
                $('.services-wrap-sum').show();
                app.tooltips();
            } else {
                $('.services-wrap-sum').hide();
                $('.doctors-table').hide();
                $('.no-service').show();

            }
            calculate();
        }, $('.comissions'));
    }

    /**
     *
     * @param fn
     * @param _this
     */
    function change(fn, _this) {

        if (!$('.total-price').length) {
            fn.apply(this, _this);
            return;
        }

        app.confirm('Текущий расчет будет сброшен. Продолжить?', function () {
            fn.apply(this, _this);
        }, function () {
        });
    }

    /**
     *
     * @param elem
     */
    function customDate(elem) {
        if (elem[0].name == 'datepick' || !elem.val()) {
            return;
        }
    }

    /**
     *
     */
    function bindDateRange() {
        var custom = $('.daterange');
        forms.daterange(custom, {
            onClose: function () {
                customDate($(this));
            }
        });
    }

    /**
     *
     */
    function handleDates() {
        var wrap = $('.custom-dates-wrap');
        wrap.attr("autocomplete", "off");
        wrap.hide();
        if (dates.val() == 'custom') {
            wrap.show();
        }
    }

//
    $(document).ready(function () {
        init();
    });

    /**
     *
     */
    return {
        calculate: function () {
            calculate();
        },
        getCosts: function(id) {
            getCosts(id)
        }
    }
})();