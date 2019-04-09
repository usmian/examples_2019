$(document).ready(function () {

    var start = new Date();

    var s = (start.getFullYear()) + "-" + ("0" + (start.getMonth() + 1)).slice(-2) + "-" + ("0" + (start.getDate() - 1)).slice(-2);


    var end = new Date();
    var e = (end.getFullYear()) + "-" + ("0" + (end.getMonth() + 1)).slice(-2) + "-" + ("0" + end.getDate()).slice(-2);

    doctors_log(
        [
            {title: "id", data: "id", visible: false},
            {title: "Клиника", data: "title", width: "15%"},
            {title: "ФИО", data: "doctor_fio", width: "15%"},
            {title: s, data: s},
            {title: e, data: e}
        ]
    ).init();
});

var doctors_log = (function ($columns) {

        /**
         * ПЕРЕМЕННЫЕ
         */
        var $table = $('#log-ajax-table');
        var $filter_form = $('#log_form');

        /**
         * ФИЛЬТРЫ
         */
        var $date_from = $('#js-cm-report_date_from');
        var $date_to = $('#js-cm-report_date_to');

        var init_plugins = function () {
            // date from
            $date_from.datepicker({
                dateFormat: "dd.mm.yy",
                altFormat: "yy-mm-dd",
                altField: "#cm_date_from"
            }).change(function () {
                if (!$(this).val()) $("#cm_date_from").val('');
            });

            var from = new Date();
            from.setDate(from.getDate() - 1);
            var format_from = $.datepicker.formatDate('dd.mm.yy', from);
            var alt_from = $.datepicker.formatDate('yy-mm-dd', from);
            var format_to = $.datepicker.formatDate('dd.mm.yy', new Date());
            $date_from.val(format_from);
            $("#cm_date_from").val(alt_from);
            // date to
            $date_to.datepicker({
                dateFormat: "dd.mm.yy",
                altFormat: "yy-mm-dd",
                altField: "#cm_date_to"
            }).change(function () {
                if (!$(this).val()) $("#cm_date_to").val('');
            });
            $date_to.val(format_to);
            var alt_to = $.datepicker.formatDate('yy-mm-dd', new Date());
            $("#cm_date_to").val(alt_to);

            $('tr td a[data-action="show-salary"]').live('click', function () {
                var that = $(this);
                renderModal(that);
            });
        };

        var get_columns = function (isInit) {

            jQuery.ajax({
                url: '/ajax/modules/dashboard/log_generate_columns.php',
                type: "POST",
                dataType: "html",
                data: $filter_form.serialize(),
                success: function (response) {
                    // убирем старую таблицу и пересоздаем новую с нужным количеством колонок
                    $table.DataTable().destroy();
                    $table.empty();

                    var res = JSON.parse(response);
                    doctors_log(res).reinit_table();
                },
                error: function (response) {
                    // Если ошибка

                }
            });
        };

        /**
         * ТАБЛИЦА
         * @type {*|jQuery}
         */
        var init_table = function () {

            $table.DataTable({
                dom: 'Bfrtip',
                ajax: {
                    url: "/ajax/modules/dashboard/doctors_log.php",
                    type: "POST",
                    dataSrc: function (json) {
                        return json.data;
                    },
                    data: function (data) {
                        data.filters = $filter_form.serialize();
                    },
                    error: function (e, message) {
                        $.notify.add(message, 'danger-msg', 15);
                    }
                },
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    nb_cols = api.columns().nodes().length;

                    var j = 3;
                    while (j < nb_cols) {
                        var pageTotal = api
                            .column(j, {page: 'current'})
                            .data()
                            .reduce(function (a, b) {
                                return Number(a) + Number(b);
                            }, 0);
                        console.log($(api.column(j)));

                        $(api.column(j).footer()).html(pageTotal);
                        j++;
                    }
                },
                columns: $columns,
                columnDefs: [
                    {
                        "render": function (data, type, row) {
                            return '<a class="link" data-action="show-salary" data-doctor_id="' + row.id_doctor_assigned + '">' + data + '</a>'
                        },
                        "targets": 2
                    }
                ],
                processing: true,
                serverSide: true,
                paging: false,
                bLengthChange: false,
                searching: false,
                ordering: false,
                language: {
                    "processing": "Подождите...",
                    "search": "Поиск:",
                    "lengthMenu": "Показать _MENU_ записей",
                    "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
                    "infoEmpty": "Записи с 0 до 0 из 0 записей",
                    "infoFiltered": "(отфильтровано из _MAX_ записей)",
                    "infoPostFix": "",
                    "loadingRecords": "Загрузка записей...",
                    "zeroRecords": "Записи отсутствуют.",
                    "emptyTable": "Нет данных",
                    "paginate": {
                        "first": "Первая",
                        "previous": "Предыдущая",
                        "next": "Следующая",
                        "last": "Последняя"
                    },
                    "aria": {
                        "sortAscending": ": активировать для сортировки столбца по возрастанию",
                        "sortDescending": ": активировать для сортировки столбца по убыванию"
                    }
                },
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title: 'Лог докторов, сводка за ' + $('#cm_date_from').val() + '',
                        footer: false
                    },
                    {
                        extend: 'excelHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title: 'Лог докторов, сводка за ' + $('#cm_date_to').val() + '',
                        footer: false
                    }
                ]
            });
        };


        function renderModal(that) {
            var doctor_id = that.attr('data-doctor_id'),
                content = $('.b-payment_list'),
                table = $('#salary_table_one'),
                //
                // в шаблоне index.list.html
                modal = $('#salary-modal'),
                dates = $filter_form.serialize();

            jQuery.ajax({
                url: '/ajax/getDoctorSalary.php',
                type: "POST",
                dataType: "html",
                data: 'iDoctorID=' + doctor_id + '&' + dates,
                success: function (response) {
                    var res = JSON.parse(response);
                    var salary = {
                        people: res.aPeopleData,
                        dates: res.dates,
                        data: res.data
                    };

                    var tpl = $('#tpl-table-salary-one');

                    html = $(_.template(tpl.html(), salary)(salary));
                    content.html(html);
                    table.DataTable({
                        paging: false,
                        bLengthChange: false,
                        searching: false,
                        ordering: false,
                        language: {
                            "processing": "Подождите...",
                            "search": "Поиск:",
                            "lengthMenu": "Показать _MENU_ записей",
                            "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
                            "infoEmpty": "Записи с 0 до 0 из 0 записей",
                            "infoFiltered": "(отфильтровано из _MAX_ записей)",
                            "infoPostFix": "",
                            "loadingRecords": "Загрузка записей...",
                            "zeroRecords": "Записи отсутствуют.",
                            "emptyTable": "Нет данных",
                            "paginate": {
                                "first": "Первая",
                                "previous": "Предыдущая",
                                "next": "Следующая",
                                "last": "Последняя"
                            },
                            "aria": {
                                "sortAscending": ": активировать для сортировки столбца по возрастанию",
                                "sortDescending": ": активировать для сортировки столбца по убыванию"
                            }
                        },
                        buttons: [
                            {
                                extend: 'pdfHtml5',
                                orientation: 'landscape',
                                pageSize: 'LEGAL',
                                title: 'Лог докторов3, сводка за ' + $('#cm_date_from').val() + '',
                                footer: false
                            },
                            {
                                extend: 'excelHtml5',
                                orientation: 'landscape',
                                pageSize: 'LEGAL',
                                title: 'Лог докторов3, сводка за ' + $('#cm_date_to').val() + '',
                                footer: false
                            }
                        ]
                    });
                    modal.show();
                }
            });
        }

        $('.close-modal').live('click', function () {
                $('#salary-modal').hide();
            }
        );

        /**
         * При поиске
         */
        $filter_form.submit(function (e) {
            e.preventDefault();
            //отлавливаем событие только 1 раз
            e.stopImmediatePropagation();
            // отдельным запросом запрашиваем нужное количество колонок
            get_columns();
            return false;
        });

        // очистка формы поиска задач
        $('.cc-btn--clear-form').click(function (e) {
            e.preventDefault();
            var $form = $(this).closest('form');

            $form.find('input, select').val(null).trigger('change');

            $table.DataTable().draw();
        });
        return {
            init: function (columns) {
                init_plugins();
                init_table(columns);
            },
            reinit_table: function (columns) {
                init_table(columns);
            }
        };

    }
);