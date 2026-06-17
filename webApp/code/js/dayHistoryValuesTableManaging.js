$(document).ready(function () {

    $.datepicker.setDefaults({ dateFormat: 'yy-mm-dd' });
    $('#datepicker_ec').datepicker();
    $('#datepicker_ph').datepicker();
    $('#datepicker_t').datepicker();

    // ── Helper: crea/ricrea una DataTable con i parametri dati ───────────────
    // onDrawn: callback opzionale chiamato dopo il primo disegno della tabella
    function buildTable(tableId, url, data, columns, onDrawn) {
        var drawn = false;
        $(tableId).DataTable({
            destroy:        true,
            bProcessing:    true,
            iDisplayLength: 4,
            dom:            'rtip',
            fnRowCallback:  function (nRow) {
                $('td', nRow).css('background-color', 'rgba(33,37,41)');
            },
            ajax: { url: url, data: data, type: 'GET' },
            aoColumns:   columns,
            columnDefs: [
                { targets: [0], visible: false, searchable: false, orderable: false },
                { targets: '_all', orderable: false }
            ],
            drawCallback: function () {
                // Eseguito dopo ogni disegno — lo usiamo solo al primo
                if (!drawn && typeof onDrawn === 'function') {
                    drawn = true;
                    onDrawn();
                }
            }
        });
    }

    var colEC   = [{ mData: 'id' }, { mData: 'data' }, { mData: 'ec' }];
    var colPH   = [{ mData: 'id' }, { mData: 'data' }, { mData: 'ph' }];
    var colT    = [{ mData: 'id' }, { mData: 'data' }, { mData: 't'  }];
    var ecUrl   = 'php/HistoryValuesIndexPage/fetch_data_ec.php';
    var phUrl   = 'php/HistoryValuesIndexPage/fetch_data_ph.php';
    var tUrl    = 'php/HistoryValuesIndexPage/fetch_data_temperature.php';

    // ── Caricamento iniziale delle tre tabelle ────────────────────────────────
    buildTable('#table_id',           ecUrl, { d: 'noData' }, colEC);
    buildTable('#ph_history',         phUrl, { d: 'noData' }, colPH);
    buildTable('#temperature_history', tUrl, { d: 'noData' }, colT);

    // ── Filtri per data ───────────────────────────────────────────────────────
    $('#filter_ec').on('click', function () {
        var d = $('#datepicker_ec').val();
        if (!d) { alert('Please select a date'); return; }
        buildTable('#table_id', ecUrl, { d: d }, colEC);
    });

    $('#filter_ph').on('click', function () {
        var d = $('#datepicker_ph').val();
        if (!d) { alert('Please select a date'); return; }
        buildTable('#ph_history', phUrl, { d: d }, colPH);
    });

    $('#filter_t').on('click', function () {
        var d = $('#datepicker_t').val();
        if (!d) { alert('Please select a date'); return; }
        buildTable('#temperature_history', tUrl, { d: d }, colT);
    });

    // ── Helper aggiunta valore + refresh tabella ──────────────────────────────
    function addValue(url, fieldId, data, tableId, colDefs) {
        $.ajax({
            url: url, type: 'post', data: data,
            success: function (response) {
                var json = response;
                if (json.status === 'true' || json.status === true) {
                    // Svuota il campo solo dopo che DataTable ha ricostruito il DOM
                    buildTable(tableId, url.replace('add_', 'fetch_data_'), { d: 'noData' }, colDefs, function () {
                        $(fieldId).val('');
                    });
                } else {
                    alert('Adding failed: ' + (json.message || ''));
                }
            },
            error: function () { alert('Network error. Please try again.'); }
        });
    }

    $(document).on('click', '.addConductivity', function (e) {
        e.preventDefault();
        var ec = $(this).closest('tr').find('input[type="number"]').val();
        if (!ec || isNaN(parseFloat(ec))) {
            alert('Please enter a valid EC value');
            return;
        }
        addValue('php/HistoryValuesIndexPage/add_ec.php',
                 '#addECField',
                 { ec: ec },
                 '#table_id', colEC);
    });

    $(document).on('click', '.addPH', function (e) {
        e.preventDefault();
        var ph = $(this).closest('tr').find('input[type="number"]').val();
        if (!ph || isNaN(parseFloat(ph))) {
            alert('Please enter a valid pH value');
            return;
        }
        addValue('php/HistoryValuesIndexPage/add_ph.php',
                 '#addPHFieldP',
                 { ph: ph },
                 '#ph_history', colPH);
    });

    $(document).on('click', '.addTemperature', function (e) {
        e.preventDefault();
        var temp = $(this).closest('tr').find('input[type="number"]').val();
        if (!temp || isNaN(parseFloat(temp))) {
            alert('Please enter a valid temperature value');
            return;
        }
        addValue('php/HistoryValuesIndexPage/add_temperature.php',
                 '#addTField',
                 { temp: temp },
                 '#temperature_history', colT);
    });

});