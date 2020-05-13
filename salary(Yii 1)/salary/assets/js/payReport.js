var payReport = (function () {

    var apiUrl;
    /**
     *
     */
    function init() {
        app.apiUrl = app.baseUrl + '/salary/api/';
        bind();

    }
    /**
     *
     */
    function bind() {
        $(document).on('click', '.show-report-details', function () {
            getDetailing($(this));
        });
    }

    /**
     *
     * @param elem
     */
    function getDetailing(elem) {
        var wrap = $('.body-wrap');

        $('.period-title').empty();
        wrap.empty();
        $('.modal-detailing').modal('show');

        //
        app.get({url: app.baseUrl + '/salary/api/', method: 'getDetailing'}, {id: elem.data('id')}, function (r) {
            if (r.error) {
                return false;
            }
            showDetailing(r.data);
            app.tooltips();
        }, $('.modal-detailing .modal-body'));
    }

    /**
     *
     * @param data
     */
    function showDetailing(data) {

        if (!data) {
            return false;
        }

        var id = data.report.salary_report_id;
        var tableData = {report: data.report, services: data.services, costs: data.costs, sum: data.sum},
            wrap = $('.body-wrap'),
            html = $(_.template($('#tpl-salary-table').html(), tableData)(tableData));
        wrap.html(html);
        forms.dataTable($('.services-table', wrap), {"tableTools": {"aButtons":[]}});
        forms.refreshGrid($('#grid-reports'));
        setComments(id);
    }

    /**
     * @param id
     */
    function setComments(id) {

        try {
            comments.init({model: 'SalaryReport', id: '', controller: 'salary/api', parent: 'SalaryReport-'});
            comments.setId(id);
        }
        catch(e) {
        }
    }

    //
    $(document).ready(function () {
        init();
    });

})();