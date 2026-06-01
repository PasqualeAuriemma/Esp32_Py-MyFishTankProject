(function($) {
    'use strict';

    $.datepicker.setDefaults({
        dateFormat: 'yy-mm-dd'
    });
    $(function() {
        $("#from_date").datepicker();
    });

    $(function() {
        $("#datepicker_ec").datepicker();
    });

    $(function() {
        $("#datepicker_ph").datepicker();
    });

    $(function() {
        $("#datepicker_t").datepicker();
    });

    $('#filter_ec').click(function() {
        var selected_date = $('#datepicker_ec').val();
        if (selected_date != '') {
            $.ajax({
                url: "filter_ec.php",
                method: "POST",
                data: { date: selected_date },
                success: function(data) {
                    $('#table_ec_history').html(data);
                }
            });
        } else {
            alert("Please Select EC Date");
        }
    });

    $('#filter_ph').click(function() {
        var selected_date = $('#datepicker_ph').val();
        if (selected_date != '') {
            $.ajax({
                url: "filter_ph.php",
                method: "POST",
                data: { date: selected_date },
                success: function(data) {
                    $('#table_ph_history').html(data);
                }
            });
        } else {
            alert("Please Select PH Date");
        }
    });

    $('#filter_t').click(function() {
        var selected_date = $('#datepicker_t').val();
        if (selected_date != '') {
            $.ajax({
                url: "filter_temp.php",
                method: "POST",
                data: { date: selected_date },
                success: function(data) {
                    $('#table_t_history').html(data);
                }
            });
        } else {
            alert("Please Select Temperature Date");
        }
    });

})(jQuery);