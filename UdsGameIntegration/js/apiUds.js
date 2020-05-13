let apiUds = {
    // Списать баллы
    purchase(data) {
        return jQuery.ajax({
            url: '/ajax/modules/dashboard/purchase_uds.php',
            type: "POST",
            dataType: "json",
            data: data
        });
    },
    // отправить код подтверждения
    sendCode(data) {
        return jQuery.ajax({
            url: '/ajax/modules/dashboard/send_uds_code.php',
            type: "POST",
            dataType: "json",
            data: data
        });
    },
    // проверить не применялись ли уже скидки к этой услуге
    checkServices(data) {
        return jQuery.ajax({
            url: '/ajax/modules/dashboard/check_services_uds.php',
            type: "POST",
            dataType: "json",
            data: data
        })
    }
}
