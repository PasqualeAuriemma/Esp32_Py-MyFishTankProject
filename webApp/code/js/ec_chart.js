(function ($) {
    'use strict';

    var chartInstance = null;

    function loadECChart(days) {
        $.post('php/Chart/ec_data_chart.php', { button: days }, function (data) {
            var name         = [];
            var marks_ec     = [];
            var marks_tds    = [];
            var marks_median = [];
            var i, j;

            for (j in data['median']) {
                marks_median.push({ x: j, y: data['median'][j].M });
            }
            for (i in data['raw']) {
                name.push('');
                marks_ec.push({ x: i, y: data['raw'][i].EC });
                marks_tds.push({ x: i, y: data['raw'][i].TDS });
            }

            var areaOptionsEC = {
                events:    ['click'],
                responsive: true,
                tooltips:  { mode: 'nearest', intersect: true },
                scales: {
                    yAxes: [{ gridLines: { color: 'rgba(204,204,204,0.1)' } }],
                    xAxes: [
                        {
                            id: 'B',
                            gridLines: { color: 'rgba(204,204,204,0.1)' }
                        },
                        {
                            id:       'x-axis-2',
                            type:     'linear',
                            position: 'bottom',
                            display:  false,
                            ticks:    { min: 0, max: marks_median.length - 1 }
                        }
                    ]
                }
            };

            var areaDataEC = {
                labels: name,
                datasets: [
                    {
                        type:            'line',
                        label:           'EC',
                        data:            marks_ec,
                        xAxisID:         'B',
                        backgroundColor: 'rgba(255,99,132,0.2)',
                        borderColor:     'rgba(255,99,132,1)',
                        borderWidth:     1,
                        pointRadius:     0,
                        fill:            true,
                    },
                    {
                        type:            'line',
                        label:           'TDS',
                        data:            marks_tds,
                        xAxisID:         'B',
                        backgroundColor: 'rgba(54,162,235,0.4)',
                        borderColor:     'rgba(54,162,235,1)',
                        borderWidth:     1,
                        pointRadius:     0,
                        fill:            true,
                    },
                    {
                        type:            'line',
                        label:           'MEAN',
                        data:            marks_median,
                        xAxisID:         'x-axis-2',
                        borderColor:     'rgba(255,159,64,1)',
                        backgroundColor: 'rgba(255,159,64,0.8)',
                        borderWidth:     2,
                        pointRadius:     0,
                        fill:            false,
                    }
                ]
            };

            var canvas = $('#areaChartEC').get(0);
            if (!canvas) { return; }

            // Distrugge il chart precedente per evitare memory leak
            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
            }

            chartInstance = new Chart(canvas.getContext('2d'), {
                type:    'line',
                data:    areaDataEC,
                options: areaOptionsEC
            });

        }, 'json');
    }

    $(document).ready(function () {
        loadECChart(1);

        $('#changeEC7D').on('click',  function () { loadECChart(7); });
        $('#changeEC1M').on('click',  function () { loadECChart(1); });
        $('#changeEC2M').on('click',  function () { loadECChart(2); });
        $('#changeECALL').on('click', function () { loadECChart(4); });
    });

})(jQuery);
