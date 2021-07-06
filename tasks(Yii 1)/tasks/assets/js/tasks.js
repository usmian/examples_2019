var tasks = (function () {
    // общие штуки
    var users = ($('#users-data').length) ? JSON.parse($('#users-data').val()) : null,
        roles = ($('#roles-data').length) ? JSON.parse($('#roles-data').val()) : null;

    var performers = $('.performers');

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
        forms.selectbox(performers);
        $('.performer-type').change(function () {
            drawPerformers();
        });

        forms.selectbox($('.responsible'));
        forms.selectbox($('.performer-type'));
        forms.selectbox($('.status'), {disable_search: true});
        forms.selectbox($('.type'));
        datetime.datepicker($('.due-date'), {defaultDate: '+0d'});
        datetime.timepicker($('.due-time'),{},'time-wrap');
        forms.scroll($('.task-history-content'));

        var form = $('#createUpdateServiceCategory');

        form.submit(function(){
            $('.loading').show();
            var inputDate = $('.due-date');
            var dueDate = inputDate.val();
            var dueTime = $('#Task_time').val();
            var dateTime = dueDate+' '+dueTime;
            inputDate.val(dateTime);
        });
    }

    /**
     *
     */
    function drawPerformers() {
        var performerType = $('.performer-type').val(),
            list = (performerType == 1) ? users : roles;
        performers.empty().append('<option></option>');
        for (var k in list) {
            var opt = $('<option></option>').attr('value', k).html(list[k]);
            performers.append(opt);
        }
        forms.updateSelectbox(performers);
    }

    //
    $(document).bind('ready', function () {
        init();
    });

    /**
     *
     */
    return {}
})();
