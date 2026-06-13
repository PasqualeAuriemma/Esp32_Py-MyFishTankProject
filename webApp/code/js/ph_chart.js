(function ($) {
    'use strict';

    var areaOptionsPH = {
        events: ['click'],
        plugins: { filler: { propagate: true } },
        scales: {
            yAxes: [{ gridLines: { color: 'rgba(204,204,204,0.1)' } }],
            xAxes: [{ gridLines: { color: 'rgba(204,204,204,0.1)' } }]
        }
    };

    var chartInstance = null; // tiene traccia dell'istanza per distruggerla prima di ricrearla

    function loadPHChart(days) {
        $.post('php/Chart/ph_data_chart.php', { button: days }, function (data) {
            var marks = [];
            var labels = [];
            for (var i in data) {
                labels.push('');
                marks.push(data[i].ph);
            }

            var canvas = $('#areaChartPH').get(0);
            if (!canvas) { return; }

            // Distrugge il chart precedente prima di crearne uno nuovo
            // per evitare memory leak e l'errore "canvas already in use"
            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
            }

            chartInstance = new Chart(canvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '# PH',
                        data:  marks,
                        backgroundColor: 'rgba(54,162,235,0.3)',
                        borderColor:     'rgba(54,162,235,1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true,
                    }]
                },
                options: areaOptionsPH
            });
        }, 'json');
    }

    $(document).ready(function () {
        loadPHChart(1); // default: 1 mese

        $('#changePH7D').on('click',  function () { loadPHChart(7); });
        $('#changePH1M').on('click',  function () { loadPHChart(1); });
        $('#changePH2M').on('click',  function () { loadPHChart(2); });
        $('#changePHALL').on('click', function () { loadPHChart(4); });
    });

})(jQuery);
