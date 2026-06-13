$(document).ready(function () {

    $.datepicker.setDefaults({ dateFormat: 'yy-mm-dd' });

    // Inizializza tutti i datepicker
    var datepickers = [
        '#datepicker_SettingsP', '#datepicker_SettingsM', '#datepicker_SettingsI',
        '#datepicker_SettingsRin', '#datepicker_SettingsPho', '#datepicker_SettingsN',
        '#datepicker_SettingsS', '#datepicker_SettingsCo2'
    ];
    $.each(datepickers, function (_, id) { $(id).datepicker(); });

    // ── Helper generico per l'invio dei volumi ────────────────────────────────
    // Evita la duplicazione degli 8 handler identici
    function submitVolume(selectName, dateId, fieldId) {
        var selected_date  = $(dateId).val();
        var selected_field = $(fieldId).val();

        if (!selected_date || !selected_field) {
            alert("Please fill in both date and volume.");
            return;
        }

        $.ajax({
            url: 'php/Volumes/add_volumes.php',
            type: 'post',
            data: { select: selectName, data: selected_date, vol: selected_field },
            success: function (data) {
                var json   = JSON.parse(data);
                var status = json.status;
                if (status === 'true') {
                    // Sostituito setInterval con setTimeout: reload una volta sola
                    setTimeout(function () { location.reload(true); }, 800);
                } else {
                    alert("Can't insert Date or Volume");
                }
            },
            error: function () {
                alert('Network error. Please try again.');
            }
        });
    }

    $('#addSettingP').on('submit', function (e) {
        e.preventDefault();
        submitVolume('Potassio',    '#datepicker_SettingsP',   '#addFieldSettingsP');
    });

    $('#addSettingsM').on('submit', function (e) {
        e.preventDefault();
        submitVolume('Magnesio',    '#datepicker_SettingsM',   '#addFieldSettingsM');
    });

    $('#addSettingsI').on('submit', function (e) {
        e.preventDefault();
        submitVolume('Ferro',       '#datepicker_SettingsI',   '#addFieldSettingsI');
    });

    $('#addSettingsRin').on('submit', function (e) {
        e.preventDefault();
        submitVolume('Rinverdente', '#datepicker_SettingsRin', '#addFieldSettingsRin');
    });

    $('#addSettingsPho').on('submit', function (e) {
        e.preventDefault();
        submitVolume('Fosforo',     '#datepicker_SettingsPho', '#addFieldSettingsPho');
    });

    $('#addSettingsStick').on('submit', function (e) {
        e.preventDefault();
        submitVolume('Stick',       '#datepicker_SettingsS',   '#addFieldSettingsS');
    });

    $('#addSettingsN').on('submit', function (e) {
        e.preventDefault();
        submitVolume('Azoto',       '#datepicker_SettingsN',   '#addFieldSettingsN');
    });

    $('#addSettingsCo2').on('submit', function (e) {
        e.preventDefault();
        submitVolume('Co2',         '#datepicker_SettingsCo2', '#addFieldSettingsCo2');
    });

});
