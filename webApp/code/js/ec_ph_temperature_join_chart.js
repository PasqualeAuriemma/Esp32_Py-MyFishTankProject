(function ($) {
    'use strict';

    var chartInstance = null;

    // ── Normalizzazione 0-1 dei valori y ──────────────────────────────────────
    function normalize(list) {
        if (!list || list.length === 0) { return []; }
        var minMax = list.reduce(function (acc, value) {
            if (value.y < acc.min) { acc.min = value.y; }
            if (value.y > acc.max) { acc.max = value.y; }
            return acc;
        }, { min: Number.POSITIVE_INFINITY, max: Number.NEGATIVE_INFINITY });

        var diff = minMax.max - minMax.min;
        return list.map(function (value) {
            if (diff === 0) { return 1 / list.length; }
            return parseFloat(((value.y - minMax.min) / diff).toFixed(2));
        });
    }

    function loadJoinChart(days) {
        $.post('php/Chart/ec_ph_temp_data_chart.php', { button: days }, function (data) {
            var name       = [];
            var marks_ec   = [];
            var marks_ph   = [];
            var marks_temp = [];
            var i, j, z;

            for (j in data['temp']) {
                marks_temp.push({ x: j, y: data['temp'][j].M_T });
            }
            for (z in data['ph']) {
                // Nota: il campo corretto è M_PH (buttonJoinALL usava erroneamente M_P)
                marks_ph.push({ x: z, y: data['ph'][z].M_PH });
            }
            for (i in data['ec']) {
                name.push('');
                marks_ec.push({ x: i, y: data['ec'][i].M });
            }

            var areaOptionsJoin = {
                events:    ['click'],
                responsive: true,
                tooltips:  { mode: 'nearest', intersect: true },
                scales: {
                    yAxes: [{ gridLines: { color: 'rgba(204,204,204,0.1)' } }],
                    xAxes: [{ id: 'B', gridLines: { color: 'rgba(204,204,204,0.1)' } }]
                }
            };

            var areaDataJoin = {
                labels: name,
                datasets: [
                    {
                        type:            'line',
                        label:           'EC',
                        data:            normalize(marks_ec),
                        xAxisID:         'B',
                        backgroundColor: 'rgba(255,99,132,0.2)',
                        borderColor:     'rgba(255,99,132,1)',
                        borderWidth:     2,
                        pointRadius:     0,
                        fill:            false,
                    },
                    {
                        type:            'line',
                        label:           'pH',
                        data:            normalize(marks_ph),
                        xAxisID:         'B',
                        backgroundColor: 'rgba(54,162,235,0.4)',
                        borderColor:     'rgba(54,162,235,1)',
                        borderWidth:     2,
                        pointRadius:     0,
                        fill:            false,
                    },
                    {
                        type:            'line',
                        label:           'Temperature',
                        data:            normalize(marks_temp),
                        xAxisID:         'B',
                        borderColor:     'rgba(255,159,64,1)',
                        backgroundColor: 'rgba(255,159,64,0.8)',
                        borderWidth:     2,
                        pointRadius:     0,
                        fill:            false,
                    }
                ]
            };

            var canvas = $('#areaChartJoin').get(0);
            if (!canvas) { return; }

            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
            }

            chartInstance = new Chart(canvas.getContext('2d'), {
                type:    'line',
                data:    areaDataJoin,
                options: areaOptionsJoin
            });

        }, 'json');
    }

    $(document).ready(function () {
        loadJoinChart(1);

        $('#changeJoin7D').on('click',  function () { loadJoinChart(7); });
        $('#changeJoin1M').on('click',  function () { loadJoinChart(1); });
        $('#changeJoin2M').on('click',  function () { loadJoinChart(2); });
        $('#changeJoinALL').on('click', function () { loadJoinChart(4); });
    });

})(jQuery);
