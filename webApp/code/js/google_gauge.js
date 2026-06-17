(function ($) {
    'use strict';

    $(document).ready(function () {
        showECGauge();
    });

    function showECGauge() {
        $.post(
            'php/Gauges/get_gauge_value.php',
            { button: 3 },
            function (data) {
                var ec_value = parseFloat(data.ValueEC) || 0;
                var ph_value = parseFloat(data.ValuePH) || 0;
                var t_value  = parseFloat(data.ValueT)  || 0;

                google.charts.load('current', { packages: ['gauge', 'corechart'] });
                google.charts.setOnLoadCallback(function () {
                    drawCharts(ec_value, ph_value, t_value);
                });
            },
            'json'  // dataType: parsa automaticamente la risposta JSON
        );
    }

    function drawCharts(ec_value, ph_value, t_value) {
        // Dimensioni responsive calcolate sul contenitore, non sulla finestra
        var containerEC = document.getElementById('chart_ec');
        if (!containerEC) { return; }

        var w = Math.floor(containerEC.offsetWidth  || 200);
        var h = Math.floor(containerEC.offsetHeight || 200);

        // ── Gauge EC ──────────────────────────────────────────────────────────
        var dataEC = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['µS/cm', ec_value],
        ]);

        var optionsEC = {
            yellowFrom:  600,
            yellowTo:    820,
            redFrom:     820,
            redTo:       1000,
            minorTicks:  10,
            max:         1000,
            height:      h,
            width:       w,
        };

        var chartEC = new google.visualization.Gauge(containerEC);
        chartEC.draw(dataEC, optionsEC);

        // ── Gauge PH ──────────────────────────────────────────────────────────
        var containerPH = document.getElementById('chart_ph');
        if (containerPH) {
            var dataPH = google.visualization.arrayToDataTable([
                ['Label', 'Value'],
                ['pH', ph_value],
            ]);
            var optionsPH = {
                greenFrom:  6.5, greenTo:  7.9,
                yellowFrom: 7.9, yellowTo: 14,
                redFrom:    0,   redTo:    6.5,
                min: 0, max: 14,
                minorTicks: 5,
                height: h, width: w,
            };
            var chartPH = new google.visualization.Gauge(containerPH);
            chartPH.draw(dataPH, optionsPH);
        }

        // ── Gauge Temperatura ─────────────────────────────────────────────────
        var containerT = document.getElementById('chart_temp');
        if (containerT) {
            var dataT = google.visualization.arrayToDataTable([
                ['Label', 'Value'],
                ['°C', t_value],
            ]);
            var optionsT = {
                yellowFrom: 28, yellowTo: 30,
                redFrom:    30, redTo:    40,
                min: 0, max: 40,
                minorTicks: 5,
                height: h, width: w,
            };
            var chartT = new google.visualization.Gauge(containerT);
            chartT.draw(dataT, optionsT);
        }
    }

})(jQuery);