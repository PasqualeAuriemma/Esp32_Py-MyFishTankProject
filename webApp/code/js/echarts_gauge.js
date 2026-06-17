(function ($) {
    'use strict';

    // ── Configurazione gauge ──────────────────────────────────────────────────
    // Ogni gauge ha: min, max, fasce colorate (axisLine.color richiede coppie
    // [percentuale_cumulativa, colore])
    var gaugeConfigs = {
        ec: {
            container: 'chart_ec',
            label:     'EC (µS/cm)',
            min: 0, max: 1000,
            splitNumber: 5,
            colorStops: [
                [600 / 1000,  '#2ecc71'], // verde 0-600
                [820 / 1000,  '#f39c12'], // arancio 600-820
                [1,           '#e74c3c'], // rosso 820-1000
            ]
        },
        ph: {
            container: 'chart_ph',
            label:     'pH',
            min: 0, max: 12,
            splitNumber: 6,
            colorStops: [
                [6.5 / 12, '#e74c3c'], // rosso (acido) 0-6.5
                [7.5 / 12, '#2ecc71'], // verde (ottimale) 6.5-7.5
                [1,        '#f39c12'], // arancio (alcalino) 7.5-12
            ]
        },
        temp: {
            container: 'chart_temp',
            label:     'Temperatura (°C)',
            min: 0, max: 45,
            splitNumber: 9,
            colorStops: [
                [28 / 45, '#2ecc71'], // verde 0-28
                [34 / 45, '#f39c12'], // arancio 28-34
                [1,       '#e74c3c'], // rosso 34-45
            ]
        }
    };

    var chartInstances = {};

    function renderGauge(key, value) {
        var config = gaugeConfigs[key];
        var container = document.getElementById(config.container);
        if (!container) { return; }

        var chart = chartInstances[key];
        if (!chart) {
            chart = echarts.init(container);
            chartInstances[key] = chart;
        }

        chart.setOption({
            series: [{
                type: 'gauge',
                min: config.min,
                max: config.max,
                splitNumber: config.splitNumber,
                radius: '90%',
                axisLine: {
                    lineStyle: {
                        width: 14,
                        color: config.colorStops
                    }
                },
                pointer: {
                    itemStyle: { color: '#3498db' }
                },
                axisTick: {
                    distance: -20,
                    length: 6,
                    lineStyle: { color: 'auto', width: 1 }
                },
                splitLine: {
                    distance: -24,
                    length: 14,
                    lineStyle: { color: 'auto', width: 2 }
                },
                axisLabel: {
                    color: '#bdc3c7',
                    distance: 28,
                    fontSize: 11
                },
                detail: {
                    valueAnimation: true,
                    fontSize: 22,
                    offsetCenter: [0, '60%'],
                    color: '#f8f9fa',
                    formatter: function (val) {
                        return val.toFixed(2);
                    }
                },
                title: {
                    fontSize: 13,
                    color: '#f8f9fa',
                    offsetCenter: [0, '85%']
                },
                data: [{ value: value, name: config.label }]
            }]
        });
    }

    function loadGauges() {
        $.post('php/Gauges/get_gauge_value.php', { button: 3 }, function (data) {
            var ec_value = parseFloat(data.ValueEC) || 0;
            var ph_value = parseFloat(data.ValuePH) || 0;
            var t_value  = parseFloat(data.ValueT)  || 0;

            renderGauge('ec',   ec_value);
            renderGauge('ph',   ph_value);
            renderGauge('temp', t_value);
        }, 'json');
    }

    // Ridimensiona i gauge quando la finestra cambia dimensione
    $(window).on('resize', function () {
        Object.keys(chartInstances).forEach(function (key) {
            chartInstances[key].resize();
        });
    });

    $(document).ready(function () {
        loadGauges();
    });

})(jQuery);
