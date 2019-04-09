let apiUds = {
    purchase(data) {
        return jQuery.ajax({
            url: '/ajax/modules/dashboard/purchase_uds.php',
            type: "POST",
            dataType: "json",
            data: data
        });
    },
    sendCode(data) {
        return jQuery.ajax({
            url: '/ajax/modules/dashboard/send_uds_code.php',
            type: "POST",
            dataType: "json",
            data: data
        });
    },
    checkServices(data) {
        return jQuery.ajax({
            url: '/ajax/modules/dashboard/check_services_uds.php',
            type: "POST",
            dataType: "json",
            data: data
        })
    }
}
