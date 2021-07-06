let uds = (function () {

    let patientInfo = null,
        companyInfo = null,
        visitID = null,
        patientID = null;

    /**
     * инициализация
     */
    function init() {
        bind();
    }

    /**
     *
     */
    function bind() {

        $(document)
            .on('click', '.uds-game-modal', function () {
                visitID = $(this).attr('data-visit_id');
                patientID = $(this).attr('data-patient_id');
                //
                document.getElementById('modal-form_uds').reset();
                //
                $('#submit-purchase').hide();
                $('#recalulate-visit').hide();
                $('#calculated-visit_uds').hide();
                $('#modal_uds_code').removeAttr('disabled');
                $('#modal_uds_scores').attr('disabled', true);
                $('#submit-udc-code').show();
                $('#info_uds').html();
                $('#calculated-visit_uds').val();
                // Проверить не применялись ли уже скидки в этот визит
                checkServicesAjax({iPatientFileID: visitID});
            });


        $(document)
            .on('click', '#submit-udc-code', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                let data = $('#modal-form_uds').serialize();
                sendCodeAjax(data);
            });

        //после отправки кода
        function activateStepCode() {
            let rate = $('#modal_uds_rate'),
                scores = $('#modal_uds_scores'),
                recalculate = $('#recalulate-visit'),
                purchase = $('#submit-purchase'),
                maxDiscount,
                maxScore,
                maxCompanyDiscount,
                current;

            $('#modal_uds_code').attr('disabled', true);
            current = $('#modal-uds_sum').html();
            current = parseFloat(current.replace(/,/g, ''));

            maxCompanyDiscount = companyInfo.marketingSettings.maxScoresDiscount;
            maxDiscount = (current / 100) * maxCompanyDiscount;
            maxScore = evaluateMaxScore(patientInfo.scores, maxDiscount);

            rate.val(maxCompanyDiscount);
            scores.val(maxScore);
            scores.prop("max", maxScore);

            //если вручную вводят больше максимума
            scores.on('change', function () {
                let val = $(this).val();
                let max = $(this).prop("max");
                if (val > parseInt(max)) {
                    $(this).val(max);
                }

            });
            // Проверка настроек скидочной компании
            switch (companyInfo.baseDiscountPolicy) {
                case 'APPLY_DISCOUNT':
                    scores.removeAttr('disabled');
                    break;
                case 'CHARGE_SCORES':
                    scores.removeAttr('disabled');
                    $('#info_uds').html('<div>Невозможно применить скидку. В настройках компании - накопление баллов!</div>');
                    break;
            }
            // форма пересчета суммы выизита
            recalculate.show();

            $(document)
                .on('click', '#recalulate-visit', function () {
                    $('#submit-udc-code').hide();
                    $('#calculated-visit_uds').val(evaluateSumVisit(scores.val(), current));
                    $('#calculated-visit_uds').show();
                    purchase.show();
                });
            //
            $(document)
                .on('click', '#submit-purchase', function (e) {
                    e.stopImmediatePropagation();
                    purchaseAjax({
                        visit_id: visitID,
                        patient_id: patientID,
                        customer: patientInfo.participantId,
                        customerID: patientInfo.id,
                        scores: $('#modal_uds_scores').val(),
                        total: $('#modal-uds_sum').html(),
                        cash: $('#calculated-visit_uds').val(),
                        code: $('#modal_uds_code').val()
                    });
                });
        }

        //
        function evaluateSumVisit(scores, sum) {
            $('#modal_uds_scores').val(parseInt(scores));
            return sum - parseInt(scores);
        }

        //
        function evaluateMaxScore(scores, maxDiscount) {
            return (scores >= maxDiscount) ? maxDiscount : scores;
        }

        // Нельзя дважды применять скидки, либо применять скидки для услуг, которые уже оплачены(даже если визит оплачен частично)
        function checkServicesAjax(data) {
            apiUds.checkServices(data).success(function (response) {
                if (!response) {
                    alert('Уже есть скидки либо оплаченные услуги')
                } else {
                    $('#modal-uds_sum').html(response.total);
                    $('#modal_uds_visit_id').val(visitID);
                    $('#uds-game-view').modal('show');
                }
            }).error(function () {
                alert('Произошла непредвиденная ошибка - попробуйте позже')
            });
        }

        // отправляем уникальный код для получения информации о пациенте и заодно компании
        function sendCodeAjax(data) {
            apiUds.sendCode(data).success(function (response) {
                if (response.patient) {
                    patientInfo = response.patient;
                    companyInfo = response.company;
                    activateStepCode();
                } else {
                    alert('Ошибка запроса к UDS. Попробуйте заново получить код');
                }
            }).error(function () {
                alert('Произошла непредвиденная ошибка - попробуйте позже')
            });
        }
        // закакзать скидки
        function purchaseAjax(data) {
            $('.loading2').show();
            apiUds.purchase(data).success(function (response) {
                $('.loading2').hide();
                $('#uds-game-view').modal('hide');
                if (response.error) {
                    alert(response.error);
                } else {
                    activateStepPurchase(response);
                }
            }).error(
                function () {
                    $('.loading2').hide();
                    $('#uds-game-view').modal('hide');
                });
        }

        function activateStepPurchase(response) {
            // Кидаем событие в дашборд для обновления ценников услуг
            $(document).trigger({
                type: 'uds-purchase_event',
                visit_id: response.visit_id,
                sum: response.services.total,
                discount_sum: response.discount_sum,
                services: response.services.services,
                percent: response.percent
            });
        }
    }

    // откатить транзакцию
    function revertServiceAjax(unique_key) {
        jQuery.ajax({
            url: '/ajax/modules/dashboard/revert_uds_transaction.php',
            type: "POST",
            dataType: "json",
            data: {unique_key: unique_key},
            success: function (response) {
                alert('Успешно')
            },
            error: function (error) {
                // todo писать в лог
                alert('Произошла непредвиденная ошибка - попробуйте позже')
            }
        });
    }

    // профиль пациенты
    function getPatientInfo() {
        return patientInfo;
    }

    // профиль компании
    function getCompanyInfo() {
        return companyInfo;
    }

    // происходит инициализация при загрузке
    $(document).ready(function () {
        init();
    });

    // Доступные извне методы
    return {
        patient: () => {
            return getPatientInfo();
        },
        company: () => {
            return getCompanyInfo();
        },
        revert: (unique_key) => {
            return revertServiceAjax(unique_key);
        }
    };
})();
