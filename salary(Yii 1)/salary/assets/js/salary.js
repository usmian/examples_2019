var salary = {

    /**
     *
     */
    init: function () {
        forms.filterAmount($('#UserSalary_value'), {mDec: '0'});
        salary.bind();
    },

    /**
     *
     */
    bind:function(){

    }
};

$(document).ready(function () {
    salary.init();
});