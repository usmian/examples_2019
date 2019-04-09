var salaryHours = (function() {

    var table,
        url = "/salary/api/getHoursReport";

    /**
     *
     */
    function init() {
        bind();
        date();
        getData();
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
        forms.selectbox($('.period'), {disable_search: true});
        $('.period').change(function () {
            handlePeriod();
        });
        handlePeriod();

        $('.btn-show').click(function () {
            getData();
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
    function getData() {
        app.get({url: url, method: ''}, getFilter(), function (r) {
            if (r.error) {
                return;
            }
            drawData(r.data);
        }, $('.list-wrap'));
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
            type: $('.type').val()
        }
    }

    /**
     * @param data
     */
    function drawData(data) {
        if (table) {
            table.destroy();
        }
        var d = {data: data, type: $('.type').val()};
        var html = $(_.template($('#tpl-hours-table').html(), d)(d));
        $('.hours-wrap').html(html);
        table = forms.dataTable($('.hours'), {
            "pageLength": 1000,
            "aLengthMenu": false,
            "sScrollX": true,
            "scrollCollapse" : true,
            "scrollY": '500px',
            "bSort": false,
            fixedColumns:   {
                leftColumns: 1,
            }
        });
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