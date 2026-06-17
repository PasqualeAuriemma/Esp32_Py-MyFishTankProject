$(document).ready(function () {

    // ── Helper generico ───────────────────────────────────────────────────────
    function addValue(url, data, errorMsg, modalId, formId) {
        $.ajax({
            url:  url,
            type: 'post',
            data: data,
            success: function (response) {
                var json = response;
                if (json.status === 'true' || response.status === true) {
                    // Chiude il modal, resetta il form, poi ricarica
                    $('#' + modalId).modal('hide');
                    $('#' + formId)[0].reset();
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
                 'Temperature adding failed',
                 'addTemperatureModal', 'addTemperatureForm');
    });

    $('#addConductivityForm').on('submit', function (e) {
        e.preventDefault();
        addValue('php/HistoryValuesIndexPage/add_ec.php',
                 { ec: $('#addECFieldP').val() },
                 'Conductivity adding failed',
                 'addConductivityModal', 'addConductivityForm');
    });

    $('#addPHForm').on('submit', function (e) {
        e.preventDefault();
        addValue('php/HistoryValuesIndexPage/add_ph.php',
                 { ph: $('#addPHFieldP').val() },
                 'Ph adding failed',
                 'addPHModal', 'addPHForm');
    });

});