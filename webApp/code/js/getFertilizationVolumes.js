(function ($) {
    'use strict';

    var gaugeInstances = {};

    // ── Colore fascia in base alla percentuale rimanente ──────────────────────
    // verde >50%, giallo 25-50%, rosso <25%
    function bandColor(pct) {
        if (pct > 50) { return '#55BF3B'; }
        if (pct > 25) { return '#DDDF0D'; }
        return '#DF5353';
    }

    // ── Crea o aggiorna un gauge Highcharts semicircolare (VU meter style) ────
    function renderVolumeGauge(containerId, name, remaining, max, label) {
        var container = document.getElementById(containerId);
        if (!container || !window.Highcharts) { return; }

        var pct       = max > 0 ? Math.max(0, (remaining / max) * 100) : 0;
        var color     = bandColor(pct);
        // Il gauge va da 0 a max, la lancetta indica "remaining"
        var gaugeVal  = Math.max(0, Math.min(max, remaining));

        var options = {
            chart: {
                type:                'gauge',
                backgroundColor:     'transparent',
                plotBackgroundColor: null,
                plotBorderWidth:     0,
                plotShadow:          false,
                margin:              [0, 0, 20, 0],
                height:              160,
                renderTo:            container
            },
            title:     null,
            credits:   { enabled: false },
            exporting: { enabled: false },
            tooltip:   { enabled: false },

            pane: {
                startAngle: -90,
                endAngle:    90,
                background: [{
                    backgroundColor: 'transparent',
                    borderWidth:     0
                }],
                center: ['50%', '90%'],
                size:   '170%'
            },

            yAxis: {
                min:  0,
                max:  max,
                tickPosition:      'outside',
                minorTickPosition: 'outside',
                tickColor:         '#888',
                tickLength:        10,
                tickWidth:         1,
                minorTickColor:    '#555',
                minorTickLength:   5,
                minorTickWidth:    1,
                minorTickInterval: 'auto',
                labels: {
                    rotation: 'auto',
                    distance: 16,
                    style:    { color: '#aaa', fontSize: '10px' }
                },
                lineWidth: 0,
                // Fascia colorata sull'arco esterno
                plotBands: [{
                    from:        0,
                    to:          gaugeVal,
                    color:       color,
                    innerRadius: '100%',
                    outerRadius: '105%'
                }],
                startOnTick: false,
                endOnTick:   false,
                title: {
                    text:  label,
                    y:    28,
                    style: { color: '#ccc', fontSize: '11px', fontWeight: 'normal' }
                }
            },

            plotOptions: {
                gauge: {
                    clip: true,
                    dataLabels: { enabled: false },
                    dial: {
                        backgroundColor: '#ffffff',
                        radius:          '100%',
                        baseWidth:       6,
                        topWidth:        1,
                        baseLength:      '10%',
                        rearLength:      '10%'
                    },
                    pivot: {
                        backgroundColor: '#ffffff',
                        radius:          4
                    }
                }
            },

            series: [{
                name: name,
                data: [gaugeVal]
            }]
        };

        if (gaugeInstances[containerId]) {
            var chart = gaugeInstances[containerId];
            // Aggiorna lancetta e fascia colorata
            chart.series[0].points[0].update(gaugeVal, false);
            chart.yAxis[0].removePlotBand('pb');
            chart.yAxis[0].addPlotBand({
                id:          'pb',
                from:        0,
                to:          gaugeVal,
                color:       color,
                innerRadius: '100%',
                outerRadius: '105%'
            });
            chart.yAxis[0].setTitle({ text: label });
            chart.redraw();
        } else {
            gaugeInstances[containerId] = new Highcharts.Chart(options);
        }
    }

    // ── Carica i dati e costruisce le card ────────────────────────────────────
    function showVolumes() {
        // Distrugge le istanze Highcharts esistenti prima di ricostruire il DOM
        // (evita il problema "gauge vuoto" alla riapertura del dialog)
        Object.keys(gaugeInstances).forEach(function (id) {
            if (gaugeInstances[id] && gaugeInstances[id].destroy) {
                gaugeInstances[id].destroy();
            }
        });
        gaugeInstances = {};

        $.getJSON('php/FertilizationVolumes/getFertilizationVolumes.php', function (data) {
            var $container = $('#volumes');
            $container.empty();

            var $row = $('<div class="row"></div>');
            $container.append($row);

            data.forEach(function (item, index) {
                var containerId = 'vol-gauge-' + index;

                var $card = $(
                    '<div class="col-sm-4 grid-margin">' +
                    '  <div class="card"><div class="card-body" style="padding:12px 12px 8px">' +
                    '    <h5 style="color:#f8f9fa;margin-bottom:4px;font-size:15px">' + item.name + '</h5>' +
                    '    <div id="' + containerId + '"></div>' +
                    '  </div></div>' +
                    '</div>'
                );
                $row.append($card);

                // Piccolo delay per assicurarsi che il DOM sia pronto
                (function(cid, it) {
                    setTimeout(function() {
                        renderVolumeGauge(cid, it.name, it.remaining, it.max, it.label);
                    }, 50 * index);
                })(containerId, item);
            });
        });
    }

    // Espone showVolumes globalmente (chiamata da index.php)
    window.showVolumes = showVolumes;

})(jQuery);