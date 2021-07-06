var tasksList = (function () {
    /**
     *
     */
    function init() {
        bind();
    }

    /**
     *
     */
    function bind() {
        $(document).on('click', '.task-confirm', function () {
            completeTask($(this).data('id'));
        });
    }

    /**
     * @param id
     */
    function completeTask(id) {
        app.confirm('Вы уверены, что хотите решить эту задачу?', function () {
            $('.loading').show();
            var msgs = {loading: 'Сохранение...', success: 'Задача решена', error: 'Ошибка сохранения'};
            app.post({url: app.baseUrl + '/tasks/api/', method: 'complete'}, {id: id}, function () {
                tasksList.update();
            }, false, msgs);
        });
    }

    //
    $(document).bind('ready', function () {
        init();
    });

    return {
        update : function () {
            forms.refreshGrid($('.grid-view'));
        }
    }
})();
