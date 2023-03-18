$(document).ready(function () {
    var start = new Date();
    var startDate = (start.getFullYear()) + "-" + ("0" + (start.getMonth() + 1)).slice(-2) + "-" + ("0" + (start.getDate() - 1)).slice(-2);
    var end = new Date();
    var endDate = (end.getFullYear()) + "-" + ("0" + (end.getMonth() + 1)).slice(-2) + "-" + ("0" + end.getDate()).slice(-2);

    doctorsLog(
        [
            {title: "id", data: "id", visible: false},
            {title: "Клиника", data: "title", width: "15%"},
            {title: "ФИО", data: "doctor_fio", width: "15%"},
            {title: startDate, data: startDate},
            {title: endDate, data: endDate}
        ]
    ).init();
});

let doctorsLog = (function ($columns) {
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

        var initPlugins = function () {
            // date from
            $date_from.datepicker({
                dateFormat: "dd.mm.yy",
                altFormat: "yy-mm-dd",
                altField: "#cm_date_from"
            }).change(function () {
                if (!$(this).val()) {
                    $("#cm_date_from").val('');
                };
            });

            $('tr td a[data-action="show-salary"]').live('click', function () {
                var that = $(this);
                renderModal(that);
            });
        };

        var getColumns = function (isInit) {
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
                    // todo
                    alert('Произошла непрдвиденная ошибка');
                }
            });
        };

        /**
         * ТАБЛИЦА
         * @type {*|jQuery}
         */
        var initTable = function () {

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
                    var nbCols = api.columns().nodes().length;

                    var j = 3;
                    while (j < nbCols) {
                        var pageTotal = api
                            .column(j, {page: 'current'})
                            .data()
                            .reduce(function (a, b) {
                                return Number(a) + Number(b);
                            }, 0);

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
            var doctorId = that.attr('data-doctor_id'),
                content = $('.b-payment_list'),
                table = $('#salary_table_one'),
                // в шаблоне index.list.html
                modal = $('#salary-modal'),
                dates = $filter_form.serialize();

            jQuery.ajax({
                url: '/ajax/getDoctorSalary.php',
                type: "POST",
                dataType: "html",
                data: 'iDoctorID=' + doctorId + '&' + dates,
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
            getColumns();
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
                initPlugins();
                initTable(columns);
            },
            reinitTable: function (columns) {
                initTable(columns);
            }
        };
    }
);
