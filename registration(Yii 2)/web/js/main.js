var resetModal = $('#reset-modal .modal-body #reset-form');


resetModal.on('submit', function (e) {
   // $('.modal-body #refresh-password').removeAttr('hidden');
    var refresh = $('.modal-body #reset-form #refresh-password');
    e.preventDefault();

    $.ajax({
        url:'site/ajax-reset',
        dataType:'JSON',
        data:$(this).serialize(),
        type:'POST',
        success:function (res) {
            //if(!res)console.log('error res');
            $('.modal-body #info-reset').html(res);
            if(res==='введите новый пароль'){
                refresh.removeAttr('readonly');
                $('.modal-body #reset-form #reset-button').text('сменить пароль');
            }
            //console.log(res);
        },
        error:function () {
            console.log('error');
        }

    });
});

