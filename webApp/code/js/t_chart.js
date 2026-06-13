(function ($) {
    'use strict';

    var areaOptionsT = {
        events: ['click'],
        plugins: { filler: { propagate: true } },
        scales: {
            yAxes: [{ gridLines: { color: 'rgba(204,204,204,0.1)' } }],
            xAxes: [{ gridLines: { color: 'rgba(204,204,204,0.1)' } }]
        }
    };

    var chartInstance = null;

    function loadTChart(days) {
        $.post('php/Chart/t_data_chart.php', { button: days }, function (data) {
            var marks  = [];
            var labels = [];
            for (var i in data) {
                labels.push('');
                marks.push(data[i].temperature);
            }

            var canvas = $('#areaChartT').get(0);
            if (!canvas) { return; }

            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
            }

            chartInstance = new Chart(canvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '# Temperature',
                        data:  marks,
                        backgroundColor: 'rgba(54,162,235,0.3)',
                        borderColor:     'rgba(54,162,235,1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true,
                    }]
                },
                options: areaOptionsT
            });
        }, 'json');
    }

    $(document).ready(function () {
        loadTChart(1);

        $('#changeT7D').on('click',  function () { loadTChart(7); });
        $('#changeT1M').on('click',  function () { loadTChart(1); });
        $('#changeT2M').on('click',  function () { loadTChart(2); });
        $('#changeTALL').on('click', function () { loadTChart(4); });
    });

})(jQuery);
