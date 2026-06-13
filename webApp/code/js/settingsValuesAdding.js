$(document).ready(function () {

    // ── Helper generico ───────────────────────────────────────────────────────
    function addValue(url, data, errorMsg) {
        $.ajax({
            url: url,
            type: 'post',
            data: data,
            success: function (response) {
                var json = JSON.parse(response);
                if (json.status === 'true') {
                    // Sostituito setInterval con setTimeout: reload una volta sola
                    setTimeout(function () { location.reload(true); }, 800);
                } else {
                    alert(errorMsg);
                }
            },
            error: function () { alert('Network error. Please try again.'); }
        });
    }

    $('#addTemperatureForm').on('submit', function (e) {
        e.preventDefault();
        addValue('php/HistoryValuesIndexPage/add_temperature.php',
            { temp: $('#addTFieldP').val() },
            'Temperature adding failed');
    });

    $('#addConductivityForm').on('submit', function (e) {
        e.preventDefault();
        addValue('php/HistoryValuesIndexPage/add_ec.php',
            { ec: $('#addECFieldP').val() },
            'Conductivity adding failed');
    });

    $('#addPHForm').on('submit', function (e) {
        e.preventDefault();
        addValue('php/HistoryValuesIndexPage/add_ph.php',
            { ph: $('#addPHFieldP').val() },
            'Ph adding failed');
    });

});
