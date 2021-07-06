kvar salaryServices = {

    /**
     *
     */
    id: null,
    changeTitle: 'Изменить тип комиссии',

    /**
     *
     */
    init: function () {
        this.id = $('#user-id').val();
        salaryServices.initServices(this.id);
        salaryServices.bind();
    },

    /**
     *
     */
    initServices: function () {
        salaryServices.render();
    },

    /**
     *
     */
    bind: function () {
        $(document).on('keydown', '.discount', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                $(this).trigger('blur');
                return false;
            }
        }).on('click', '.all-comission-save', function () {
            salaryServices.setAll('all');
        }).on('click', '.all-recommendation-save', function () {
            salaryServices.setAll('rec');
        });
    },
    /**
     *
     */
    render: function () {
        app.currencyShort = 'руб.';
        var isDoctor = $('.is-doctor').val(),
            order = 7;

        if (isDoctor.length) {
            salaryServices.renderDoctorsInput();
            order = 8;
        }

        var container = $('.filters td:nth-child(' + order + ')');
        container.addClass('all-discount');

        //
        var data = {wrap_class: 'rec', btn_class: 'recommendation'};
        var html = _.template($('#tpl-discount').html(), data)(data);
        container.html(html);

        //
        forms.selectbox($('#Service_service_category_id'));
        forms.selectbox($('#Service_profession_id'));
        forms.discount($('.discount-wrap-rec'), {
            change: function (e) {
                if (!e) {
                    return;
                }
                var serviceDiscountType = $(this).closest($('.discount-wrap-all')).data('type'),
                    serviceDiscount = $(this).val();
                if (serviceDiscountType === 2) {
                    forms.clearAmount(serviceDiscount);
                }
            },
            changeTitle: this.changeTitle
        });

        //
        $('.discount-current').each(function () {
            forms.discount($(this), {
                    change: function (e) {
                        if (!e) {
                            return;
                        }
                        var serviceDiscountType = $(this).closest($('.discount-wrap')).data('type'),
                            serviceDiscount = $(this).val(),
                            serviceId = $(this).data('service_id'),
                            sid = $(this).closest($('.discount-wrap')).data('sid'),
                            recsid = $(this).closest($('.discount-wrap')).data('recsid');

                        if (serviceDiscountType === 2) {
                            forms.clearAmount(serviceDiscount);
                        }
                        salaryServices.update(serviceId,
                            serviceDiscount, serviceDiscountType, sid, recsid);
                    },
                    spin: function () {
                        var that = $(this);
                        setTimeout(function () {
                            var serviceDiscountType = $(that).closest($('.discount-wrap')).data('type'),
                                serviceDiscount = $(that).val(),
                                serviceId = $(that).data('service_id'),
                                sid = $(that).closest($('.discount-wrap')).data('sid'),
                                recsid = $(that).closest($('.discount-wrap')).data('recsid');
                            if (serviceDiscountType === 2) {
                                forms.clearAmount(serviceDiscount);
                            }
                            salaryServices.update(serviceId,
                                serviceDiscount, serviceDiscountType, sid, recsid);
                        }, 0);
                    },
                    price: $(this).data('price'),
                    changeTitle: salaryServices.changeTitle
                }
            );
        });
    },

    /**
     *
     */
    renderDoctorsInput: function () {
        var container = $('.filters td:nth-child(7)');
        container.addClass('doctors-discount');
        //
        var data = {wrap_class: 'all', btn_class: 'comission'};
        var html = _.template($('#tpl-discount').html(), data)(data);
        container.html(html);
        //
        forms.discount($('.discount-wrap-all'), {
            change: function (e) {
                if (!e) {
                    return;
                }
                var serviceDiscountType = $(this).closest($('.discount-wrap-all')).data('type'),
                    serviceDiscount = $(this).val();
                if (serviceDiscountType === 2) {
                    forms.clearAmount(serviceDiscount);
                }
            },
            changeTitle: this.changeTitle
        });
    },

    /**
     * @param id
     * @param value
     * @param type
     * @param serviceId
     * @param recId
     */
    update: function (id, value, type, serviceId, recId) {
        app.post({url: app.baseUrl + '/salary/api/', method: 'updateService'}, {
            user_salary_id: id,
            value: value,
            type: type,
            user_id: salaryServices.id,
            service_id: serviceId,
            rs_id: recId
        }, function () {
            //forms.refreshGrid($('#grid-salary-settings'));
        }, false, {
            'error': 'Ошибка сохранения',
            'success': 'Комиссия сохранена',
            'loading': 'Сохранение...'
        });
    },

    /**
     * @param valueType
     */
    setAll: function (valueType) {
        app.confirm('Вы уверены, что хотите задать комиссию всем выбранным услугам?', function () {
            var wrapAll = $('.discount-wrap-' + valueType),
                type = wrapAll.data('type'),
                value = $('.discount', wrapAll).val(),
                service = $('#Service_name').val(),
                code = $('#Service_code').val(),
                professionId = $('#Service_profession_id').val(),
                categoryId = $('#Service_service_category_id').val();

            salaryServices.updateAll(
                salaryServices.id, value, code, service, categoryId, type, professionId,
                valueType == 'rec' ? 1 : 0
            );
        });
    },

    /**
     * @param id
     * @param value
     * @param code
     * @param service
     * @param categoryId
     * @param professionId
     * @param type
     * @param recommend
     */
    updateAll: function (id, value, code, service, categoryId, type, professionId, recommend) {
        app.post({url: app.baseUrl + '/salary/api/', method: 'updateAllServices'}, {
            userId: id,
            value: value,
            code: code,
            service: service,
            categoryId: categoryId,
            type: type,
            professionId: professionId,
            recommend: recommend
        }, function () {
            forms.refreshGrid($('#grid-salary-settings'));
        }, false, {
            'error': 'Ошибка запроса',
            'success': 'Комиссия сохранена',
            'loading': 'Подождите...'
        });
    }
};

$(document).ready(function () {
    salaryServices.init();
});
