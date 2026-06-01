(function($) {
    'use strict';

$(document).ready(function () {
            showGraph();
          
        });

        function showGraph()
        {
            {
                $.post("ph_data_chart.php",
                function (data)
                {
                    console.log(data);
                     var name = [];
                    var marks = [];

                    for (var i in data) {
                        name.push("");
                        marks.push(data[i].ph);
                    }

                    var areaDataPH = {
                labels: ["","",""],
                datasets: [{
                    label: '# of Votes',
                    data: [55,22,44],
                    backgroundColor: 'rgb(54, 162, 235, 0.3)',
                    borderColor: 'rgb(54, 162, 235, 1)',
                    borderWidth: 1,
                    pointRadius: 0,
                    fill: true, // 3: no fill
                }]
            };
            var areaOptionsPH = {
                plugins: {
                    filler: {
                        propagate: true
                    }
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            color: "rgba(204, 204, 204,0.1)"
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            color: "rgba(204, 204, 204,0.1)"
                        }
                    }]
                }
            }
            var areaChartCanvas = $("#graphCanvas").get(0).getContext("2d");
            var areaChartPH = new Chart(areaChartCanvas, {
                type: 'line',
                data: areaDataPH,
                options: areaOptionsPH
            });
                    
                });
            }
        }
        
        })(jQuery);