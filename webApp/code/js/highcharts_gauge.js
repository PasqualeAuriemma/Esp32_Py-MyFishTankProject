(function ($) {
    'use strict';

    // ── Fasce a blocchi precalcolate (8 blocchi per fascia, gap 15%) ──────────
    // Ogni blocco = una plotBand separata con piccolo gap tra l'una e l'altra
    var blockBands = {
        ec: [
            {from:5.625,to:69.375,color:'#55BF3B',thickness:'20%'},
            {from:80.625,to:144.375,color:'#55BF3B',thickness:'20%'},
            {from:155.625,to:219.375,color:'#55BF3B',thickness:'20%'},
            {from:230.625,to:294.375,color:'#55BF3B',thickness:'20%'},
            {from:305.625,to:369.375,color:'#55BF3B',thickness:'20%'},
            {from:380.625,to:444.375,color:'#55BF3B',thickness:'20%'},
            {from:455.625,to:519.375,color:'#55BF3B',thickness:'20%'},
            {from:530.625,to:594.375,color:'#55BF3B',thickness:'20%'},
            {from:602.0625,to:625.4375,color:'#DDDF0D',thickness:'20%'},
            {from:629.5625,to:652.9375,color:'#DDDF0D',thickness:'20%'},
            {from:657.0625,to:680.4375,color:'#DDDF0D',thickness:'20%'},
            {from:684.5625,to:707.9375,color:'#DDDF0D',thickness:'20%'},
            {from:712.0625,to:735.4375,color:'#DDDF0D',thickness:'20%'},
            {from:739.5625,to:762.9375,color:'#DDDF0D',thickness:'20%'},
            {from:767.0625,to:790.4375,color:'#DDDF0D',thickness:'20%'},
            {from:794.5625,to:817.9375,color:'#DDDF0D',thickness:'20%'},
            {from:821.6875,to:840.8125,color:'#DF5353',thickness:'20%'},
            {from:844.1875,to:863.3125,color:'#DF5353',thickness:'20%'},
            {from:866.6875,to:885.8125,color:'#DF5353',thickness:'20%'},
            {from:889.1875,to:908.3125,color:'#DF5353',thickness:'20%'},
            {from:911.6875,to:930.8125,color:'#DF5353',thickness:'20%'},
            {from:934.1875,to:953.3125,color:'#DF5353',thickness:'20%'},
            {from:956.6875,to:975.8125,color:'#DF5353',thickness:'20%'},
            {from:979.1875,to:998.3125,color:'#DF5353',thickness:'20%'}
        ],
        ph: [
            {from:0.0609,to:0.7516,color:'#DF5353',thickness:'20%'},
            {from:0.8734,to:1.5641,color:'#DF5353',thickness:'20%'},
            {from:1.6859,to:2.3766,color:'#DF5353',thickness:'20%'},
            {from:2.4984,to:3.1891,color:'#DF5353',thickness:'20%'},
            {from:3.3109,to:4.0016,color:'#DF5353',thickness:'20%'},
            {from:4.1234,to:4.8141,color:'#DF5353',thickness:'20%'},
            {from:4.9359,to:5.6266,color:'#DF5353',thickness:'20%'},
            {from:5.7484,to:6.4391,color:'#DF5353',thickness:'20%'},
            {from:6.5094,to:6.6156,color:'#55BF3B',thickness:'20%'},
            {from:6.6344,to:6.7406,color:'#55BF3B',thickness:'20%'},
            {from:6.7594,to:6.8656,color:'#55BF3B',thickness:'20%'},
            {from:6.8844,to:6.9906,color:'#55BF3B',thickness:'20%'},
            {from:7.0094,to:7.1156,color:'#55BF3B',thickness:'20%'},
            {from:7.1344,to:7.2406,color:'#55BF3B',thickness:'20%'},
            {from:7.2594,to:7.3656,color:'#55BF3B',thickness:'20%'},
            {from:7.3844,to:7.4906,color:'#55BF3B',thickness:'20%'},
            {from:7.5422,to:8.0203,color:'#DDDF0D',thickness:'20%'},
            {from:8.1047,to:8.5828,color:'#DDDF0D',thickness:'20%'},
            {from:8.6672,to:9.1453,color:'#DDDF0D',thickness:'20%'},
            {from:9.2297,to:9.7078,color:'#DDDF0D',thickness:'20%'},
            {from:9.7922,to:10.2703,color:'#DDDF0D',thickness:'20%'},
            {from:10.3547,to:10.8328,color:'#DDDF0D',thickness:'20%'},
            {from:10.9172,to:11.3953,color:'#DDDF0D',thickness:'20%'},
            {from:11.4797,to:11.9578,color:'#DDDF0D',thickness:'20%'}
        ],
        temp: [
            {from:0.2625,to:3.2375,color:'#55BF3B',thickness:'20%'},
            {from:3.7625,to:6.7375,color:'#55BF3B',thickness:'20%'},
            {from:7.2625,to:10.2375,color:'#55BF3B',thickness:'20%'},
            {from:10.7625,to:13.7375,color:'#55BF3B',thickness:'20%'},
            {from:14.2625,to:17.2375,color:'#55BF3B',thickness:'20%'},
            {from:17.7625,to:20.7375,color:'#55BF3B',thickness:'20%'},
            {from:21.2625,to:24.2375,color:'#55BF3B',thickness:'20%'},
            {from:24.7625,to:27.7375,color:'#55BF3B',thickness:'20%'},
            {from:28.0562,to:28.6937,color:'#DDDF0D',thickness:'20%'},
            {from:28.8062,to:29.4437,color:'#DDDF0D',thickness:'20%'},
            {from:29.5562,to:30.1937,color:'#DDDF0D',thickness:'20%'},
            {from:30.3062,to:30.9437,color:'#DDDF0D',thickness:'20%'},
            {from:31.0562,to:31.6937,color:'#DDDF0D',thickness:'20%'},
            {from:31.8062,to:32.4438,color:'#DDDF0D',thickness:'20%'},
            {from:32.5562,to:33.1938,color:'#DDDF0D',thickness:'20%'},
            {from:33.3062,to:33.9438,color:'#DDDF0D',thickness:'20%'},
            {from:34.1031,to:35.2719,color:'#DF5353',thickness:'20%'},
            {from:35.4781,to:36.6469,color:'#DF5353',thickness:'20%'},
            {from:36.8531,to:38.0219,color:'#DF5353',thickness:'20%'},
            {from:38.2281,to:39.3969,color:'#DF5353',thickness:'20%'},
            {from:39.6031,to:40.7719,color:'#DF5353',thickness:'20%'},
            {from:40.9781,to:42.1469,color:'#DF5353',thickness:'20%'},
            {from:42.3531,to:43.5219,color:'#DF5353',thickness:'20%'},
            {from:43.7281,to:44.8969,color:'#DF5353',thickness:'20%'}
        ]
    };

    var gaugeConfigs = {
        ec:   { container:'chart_ec',   title:'EC',          unit:'µS/cm', min:0,   max:1000, tickInterval:200 },
        ph:   { container:'chart_ph',   title:'pH',          unit:'',      min:0,   max:12,   tickInterval:2   },
        temp: { container:'chart_temp', title:'Temperatura', unit:'°C',    min:0,   max:45,   tickInterval:5   }
    };

    var chartInstances = {};

    function renderGauge(key, value) {
        var config    = gaugeConfigs[key];
        var container = document.getElementById(config.container);
        if (!container || !window.Highcharts) { return; }

        var options = {
            chart: {
                type:'gauge', backgroundColor:'transparent',
                plotBackgroundColor:null, plotBorderWidth:0, plotShadow:false,
                margin:[0,0,0,0], renderTo:container
            },
            title:null, credits:{enabled:false}, exporting:{enabled:false},

            pane: {
                startAngle:-50, endAngle:230,
                background:[
                    {backgroundColor:{linearGradient:{x1:0,y1:0,x2:0,y2:1},stops:[[0,'#FFF'],[1,'#333']]},borderWidth:0,outerRadius:'109%'},
                    {backgroundColor:{linearGradient:{x1:0,y1:0,x2:0,y2:1},stops:[[0,'#333'],[1,'#FFF']]},borderWidth:1,outerRadius:'107%'},
                    {backgroundColor:'#000',borderWidth:0,outerRadius:'105%'},
                    {backgroundColor:'#000',borderWidth:0,outerRadius:'105%',innerRadius:'103%'}
                ]
            },

            yAxis: {
                min:config.min, max:config.max,
                tickPixelInterval:40, tickInterval:config.tickInterval,
                tickPosition:'inside', tickColor:'#cccccc', tickLength:20, tickWidth:2,
                minorTickInterval:'auto', minorTickPosition:'inside',
                minorTickColor:'#666', minorTickLength:10, minorTickWidth:1,
                labels:{ step:2, distance:20, rotation:'auto', style:{color:'#cccccc',fontSize:'11px'} },
                plotBands: blockBands[key],
                title:{ text:config.unit, y:90, style:{color:'#cccccc',fontSize:'11px'} }
            },

            series:[{
                name:config.title,
                data:[value],
                dataLabels:{
                    // ── Valore in basso a sinistra ────────────────────────────
                    format:     '{y:.2f}' + (config.unit ? ' ' + '' : ''),
                    borderWidth:0,
                    x:          -95,
                    y:          -15,
                    style:{ color:'#ffffff', fontSize:'15px', fontWeight:'bold', textOutline:'none' }
                },
                dial:{
                    radius:'80%', backgroundColor:'white',
                    baseWidth:12, topWidth:1, baseLength:'0%', rearLength:'0%'
                },
                pivot:{ backgroundColor:'white', radius:6 }
            }],

            tooltip:{ enabled:false }
        };

        if (chartInstances[key]) {
            chartInstances[key].series[0].points[0].update(value, true, {duration:800});
        } else {
            chartInstances[key] = new Highcharts.Chart(options);
        }
    }

    function loadGauges() {
        $.post('php/Gauges/get_gauge_value.php', {button:3}, function(data) {
            renderGauge('ec',   parseFloat(data.ValueEC) || 0);
            renderGauge('ph',   parseFloat(data.ValuePH) || 0);
            renderGauge('temp', parseFloat(data.ValueT)  || 0);
        }, 'json');
    }

    $(window).on('resize', function() {
        Object.keys(chartInstances).forEach(function(key) {
            if (chartInstances[key]) { chartInstances[key].reflow(); }
        });
    });

    $(document).ready(function() { loadGauges(); });

})(jQuery);
