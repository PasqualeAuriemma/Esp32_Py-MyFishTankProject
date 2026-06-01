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

          
          var areaData1 = {
            labels: [<?php 
                        $result1E = $con->query($quer2EC);
                        if ($result1E->num_rows > 0) {
                           // output data of each row
                           while($row1E = $result1E->fetch_assoc()) {
                                echo "'',";
                           }
                         }?>],
            datasets: [{
               type: 'line',
               label: '# EC',
               data: [<?php 
                       $result1E_1 = $con->query($quer2EC);
                       $x = 0;
                       if ($result1E_1->num_rows > 0) {
                          // output data of each row
                          while($row1E = $result1E_1->fetch_assoc()) {
                            $x = $x + 1;
                            echo "{x: ".$x.", y: ".$row1E['E']."},";
                          }
                       }?>],        
               backgroundColor: 'rgba(255, 99, 132, 0.2)',
               borderColor: 'rgba(255,99,132, 1)',
               borderWidth: 1,
               pointRadius: 0,
               fill: true, // 3: no fill
               }, {
                    type: 'line',
                    label: '# TDS',
                    data: [<?php 
                            $result1E_2 = $con->query($quer2EC);
                            $x = 0;
                            if ($result1E_2->num_rows > 0) {
                              // output data of each row
                              while($row1E_2 = $result1E_2->fetch_assoc()) {
                                $x = $x + 1;
                                echo "{x: ".$x.", y: ".$row1E_2['T']."},";
                              }
                            }?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.4)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    pointRadius: 0,
                    fill: true, // 3: no fill
                  }, {  
                        type: 'line',
                        label: '# DAILY AVARAGE EC',
                        data: [{ x: 1, y: 7 }, { x: 2, y: 14 },
                         { x: 4, y: 4 }, { x: 5, y: 15 },
                         { x: 7, y: 15 }, { x: 9, y: 15 }], 
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        pointRadius: 0,
                        xAxisID: 'x-axis-2',
                        borderWidth: 2,
                        fill: false, // 3: no fill
                      }]
            };

            var areaOptions = {
                responsive: true,
                title: {
                    display: true,
                    text: 'Conductivity and TDS monitoring'
                },
                tooltips: {
                    mode: 'nearest',
                    intersect: true,
                },
                legend: {
                    labels: {
                        useLineStyle: true
                    },
                    //position: 'bottom',
                },
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
                    }, {
                        id: 'x-axis-2',
                        type: 'linear',
                        position: 'bottom',
                        display: false,           
                    }]
                }
            }

            var areaChartCanvas = $("#areaChartEC").get(0).getContext("2d");
            var areaChart = new Chart(areaChartCanvas, {
                type: 'line',
                data: areaData1,
                options: areaOptions
            });

            var buttonEC7D = document.getElementById('changeEC7D');
            var buttonEC1M = document.getElementById('changeEC1M');
            var buttonEC2M = document.getElementById('changeEC2M');
            var buttonECALL = document.getElementById('changeECALL');

            buttonEC7D.onclick = function() {
                var areaDataEC7D = {
                    labels: ["", "", "", "", "", "", "", "", "", "",],
                    datasets: [{
                        type: 'line',
                        label: '# EC',
                        data: [{ x: 1, y: 27 }, { x: 2, y: 24 },
                            { x: 3, y: 24 }, { x: 4, y: 25 },
                            { x: 5, y: 25 }, { x: 6, y: 23 },
                            { x: 7, y: 20 }, { x: 8, y: 13 },
                            { x: 9, y: 15 }, { x: 10, y: 16 },],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255,99,132, 1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true, // 3: no fill
                    }, {
                          type: 'line',
                          label: '# TDS',
                          data: [{ x: 1, y: 17 }, { x: 2, y: 14 },
                              { x: 3, y: 14 }, { x: 4, y: 15 },
                              { x: 5, y: 15 }, { x: 6, y: 13 },
                              { x: 7, y: 10 }, { x: 8, y: 3 },
                              { x: 9, y: 5 }, { x: 10, y: 6 },],
                          backgroundColor: 'rgba(54, 162, 235, 0.4)',
                          borderColor: 'rgba(54, 162, 235, 1)',
                          borderWidth: 1,
                          pointRadius: 0,
                          fill: true, // 3: no fill
                       }, {  
                             type: 'line',
                             label: '# DAILY AVARAGE EC',
                             data: [{ x: 1, y: 7 }, { x: 2, y: 14 },
                                    { x: 4, y: 4 }],
                             borderColor: 'rgba(255, 159, 64, 1)',
                             backgroundColor: 'rgba(255, 159, 64, 0.8)',
                             pointRadius: 0,
                             xAxisID: 'x-axis-2',
                             borderWidth: 2,
                             fill: false, // 3: no fill
                           }]
                };
                areaChart.clear();
                areaChart.data = areaDataEC7D;
                areaChart.update();
            };

            buttonEC1M.onclick = function() {
               var areaDataEC1M = {
                    labels: ["", "", "", "", "", "", "", "", "", "", "", "", ""],
                    datasets: [{
                        type: 'line',
                        label: '# EC',
                        data: [{ x: 1, y: 27 }, { x: 2, y: 24 },
                            { x: 3, y: 24 }, { x: 4, y: 25 },
                            { x: 5, y: 25 }, { x: 6, y: 23 },
                            { x: 7, y: 20 }, { x: 8, y: 13 },
                            { x: 9, y: 15 }, { x: 10, y: 16 },
                            { x: 11, y: 15 }, { x: 12, y: 16 },
                            { x: 13, y: 15 }
                        ],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255,99,132, 1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true, // 3: no fill
                    }, {
                        type: 'line',
                        label: '# TDS',
                        data: [{ x: 1, y: 17 }, { x: 2, y: 14 },
                            { x: 3, y: 14 }, { x: 4, y: 15 },
                            { x: 5, y: 15 }, { x: 6, y: 13 },
                            { x: 7, y: 10 }, { x: 8, y: 3 },
                            { x: 9, y: 5 }, { x: 10, y: 6 },
                            { x: 11, y: 5 }, { x: 12, y: 6 },
                            { x: 13, y: 5 }
                        ],
                        backgroundColor: 'rgba(54, 162, 235, 0.4)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true, // 3: no fill
                    }, {
                        type: 'line',
                        label: '# DAILY AVARAGE EC',
                        data: [{ x: 1, y: 7 }, { x: 2, y: 14 },
                            { x: 4, y: 4 }, { x: 6, y: 8 },
                        ],
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        pointRadius: 0,
                        xAxisID: 'x-axis-2',
                        borderWidth: 2,
                        fill: false, // 3: no fill
                    }]

                };
                areaChart.clear();
                areaChart.data = areaDataEC1M;
                areaChart.update();

            };

            buttonEC2M.onclick = function() {
                areaChart.clear();
                //areaChart.data = areaDataEC1M;
                areaChart.update();
            };

            buttonECALL.onclick = function() {
                areaChart.clear();
                //areaChart.data = areaDataEC1M;
                areaChart.update();
            };

        
  
                    
                });
            }
        }
        
        })(jQuery);