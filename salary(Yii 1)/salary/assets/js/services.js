var salaryServices = (function() {

    var table,
        url = "/salary/api/getServicesReport";

    /**
     *
     */
    function init() {
        bind();
        date();
        initTable();
    }

    /**
     *
     */
    function bind() {
        $('.selectbox').each(function () {
            if ($(this).hasClass('no-selectbox')) {
                return;
            }
            var params = {};
            var placeholder = $(this).data('placeholder');
            if (placeholder) {
                params.placeholder_text_multiple = placeholder;
            }
            forms.selectbox($(this), params);
        });

        //
        forms.servicesList($('.service'), {}, '[Услуга]', {allowClear: true});

        //
        forms.selectbox($('.period'), {disable_search: true});
        $('.period').change(function () {
            handlePeriod();
        });
        handlePeriod();

        $('.btn-show').click(function () {
            reloadData();
        });

        $('.download').click(function () {
            download();
        });
    }

    /**
     *
     */
    function handlePeriod() {
        var period = $('.period').val(),
            wrap = $('.from-wrap');
        wrap.hide();
        if (period == 'custom') {
            wrap.show();
        }
    }

    /**
     *
     */
    function date() {
        var datesWrap = $('.dates'),
            dateCheck = $('.date-confirm');
        datesWrap.val(datetime.getTimeValue(null, '-1 month', 'DD.MM.YYYY') + ' - ' + datetime.getNow('DD.MM.YYYY'));
        dateCheck.val(datetime.getTimeValue(null, '', 'DD.MM.YYYY'));
        forms.daterange(datesWrap);
        forms.datepicker(dateCheck);
    }

    /**
     *
     */
    function initTable() {
        table = forms.dataTable($('.table'), {
            "processing": true,
            "serverSide": true,
            "order": [[0, "asc"]],
            "ajax": {
                url: url,
                data: function (data) {
                    $.extend(data, getFilter());
                }
            },
            "aoColumns": [
                {mData: 'date'},
                {mData: 'doctor'},
                {mData: 'clinic'},
                {mData: 'invoice'},
                {mData: 'company_title'},
                {mData: 'service_code'},
                {mData: 'service_title', render: function (data) {
                    return "<span class='salary-service-title'>"+data+"</span>";
                }},
                {mData: 'full_value'},
                {mData: 'count'},
                {mData: 'discount'},
                {mData: 'value'},
                {mData: 'patient'},
                {mData: 'type_patient'},
                {mData: 'payment_type_title'},
                {mData: 'payment_date'},
                {mData: 'debt_title'},
                {mData: 'recommendation'},
            ],
            "tableTools": {
                "aButtons": []
            },
            "pageLength": 25,
            "aLengthMenu": [
                [25, 50, 100],
                [25, 50, 100]
            ],
            "sScrollX": '100%',
            "scrollCollapse" : true,
            "scrollY": false,
            "bSort": false,
        });
    }

    
    /**
     * @return
     */
    function getFilter() {
        return {
            period: $('.period').val(),
            dates: $('.dates').val(),
            clinic: $('.clinic').val(),
            doctor: $('.doctor').val(),
            professions: $('.professions').val(),
            recommendation: $('.recommendation').val(),
            service: $('.service').val(),
            company: $('.company').val(),
            payment: $('.payment').val(),
            type: $('.patient-type').val()
        }
    }

    /**
     *
     */
    function reloadData() {
        table.ajax.reload();
    }

    /**
     *
     */
    function download() {
        var params = getFilter();
        params['download'] = 1;
        var _url = url + '?' + $.param(params);
        app.redirect(_url, true);
    }

    //
    $(document).ready(function () {
        init();
    });

    //
    return {

    };

})();