(function($) {
    'use strict';
  $(document).ready(function () {
    showGraphEC();
  });
  
 var buttonEC7D = document.getElementById('changeEC7D');
 var buttonEC1M = document.getElementById('changeEC1M');
 var buttonEC2M = document.getElementById('changeEC2M');
 var buttonECALL = document.getElementById('changeECALL');

 buttonEC7D.onclick = function () {
      $.post("php/Chart/ec_data_chart.php",
             {button:7},
             function (data){
               var name = [];
               var marks_ec = [];
               var marks_tds = [];
               var marks_median = [];
               var j = 0;
               var i = 0;
               for (j in data['median']) {
                 marks_median.push({x:j, y:data['median'][j].M});
               }
               for (i in data['raw']) {
                 name.push("");
                 marks_ec.push({x:i, y:data['raw'][i].EC});
                 marks_tds.push({x:i, y:data['raw'][i].TDS});
               }
               var step = Math.trunc(i/j);
         
               var areaOptionsEC = {
                 events: ['click'],
                 responsive: true,
                
                 tooltips: {
                    mode: 'nearest',
                    intersect: true,
                 },          
                 scales: { 
                   yAxes: [{gridLines: {color: "rgba(204, 204, 204,0.1)"}}],
                   xAxes: [{id: 'B',
                        gridLines: {
                            color: "rgba(204, 204, 204,0.1)"
                        }
                      }, {
                        id: 'x-axis-2',
                        type: 'linear',
                        position: 'bottom',
                        display: false,
                        ticks: {
                                min: 0,
                                max: (marks_median.length - 1),
                        } 
                    }]
                  }
               }
   
               
               var areaDataEC = {
                      labels: name,
                      datasets: [{
                        type: 'line',
                        label: 'EC',
                        data: marks_ec,
                        xAxisID: 'B',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255,99,132, 1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'TDS',
                        data: marks_tds,
                        xAxisID: 'B',
                        backgroundColor: 'rgba(54, 162, 235, 0.4)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'MEAN',
                        data:marks_median,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        pointRadius: 0,
                        xAxisID: 'x-axis-2',
                        borderWidth: 2,
                        fill: false, // 3: no fill
                    }]
                   };
                   //areaChart.clear();
                   //areaChart.data = areaDataT7D;
                   //areaChart.update();
                   var areaChartECCanvas = $("#areaChartEC").get(0).getContext("2d");
                   var areaChartEC = new Chart(areaChartECCanvas, {
                     type: 'line',
                     data: areaDataEC,
                     options: areaOptionsEC
                   });
         });
       };
       
       buttonEC1M.onclick = function () {
         $.post("php/Chart/ec_data_chart.php",
                {button:1},
                function (data){
                   var name = [];
                   var marks_ec = [];
                   var marks_tds = [];
                   var marks_median = [];
                   var i = 0;
                   var j = 0;  
                   for (j in data['median']) {
                     marks_median.push({x:j, y:data['median'][j].M});
                   }
                   for (i in data['raw']) {
                     name.push("");
                     marks_ec.push({x:i, y:data['raw'][i].EC});
                     marks_tds.push({x:i, y:data['raw'][i].TDS});
                   }
                   var step = Math.trunc(i/j);
                   var areaOptionsEC = {
                     events: ['click'],
                     responsive: true,
                     
                       tooltips: {
                          mode: 'nearest',
                          intersect: true,
                       },          
                       scales: { 
                         yAxes: [{gridLines: {color: "rgba(204, 204, 204,0.1)"}}],
                         xAxes: [{id: 'B',
                              gridLines: {
                                  color: "rgba(204, 204, 204,0.1)"
                              }
                            }, {
                              id: 'x-axis-2',
                              type: 'linear',
                              position: 'bottom',
                              display: false,
                              ticks: {
                                min: 0,
                                max: (marks_median.length - 1),
                              } 
                          }]
                        }
                     }
                    var areaDataEC = {
                      labels:name,
                      datasets: [{
                        type: 'line',
                        label: 'EC',
                         xAxisID: 'B',
                        data: marks_ec,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255,99,132, 1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'TDS',
                        data: marks_tds,
                         xAxisID: 'B',
                        backgroundColor: 'rgba(54, 162, 235, 0.4)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'MEAN',
                        data: marks_median,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        pointRadius: 0,
                        xAxisID: 'x-axis-2',
                        borderWidth: 2,
                        fill: false, // 3: no fill
                      }]
                   };
                   //areaChart.clear();
                   //areaChart.data = areaDataT7D;
                   //areaChart.update();
                   var areaChartECCanvas = $("#areaChartEC").get(0).getContext("2d");
                   var areaChartEC = new Chart(areaChartECCanvas, {
                     type: 'line',
                     data: areaDataEC,
                     options: areaOptionsEC
                   });
         });
       };
       
       buttonEC2M.onclick = function () {
          $.post("php/Chart/ec_data_chart.php",
                  {button:2},
                  function (data){
                    var name = [];
                    var marks_ec = [];
                    var marks_tds = [];
                    var marks_median = [];
                    var i = 0;
                    var j = 0;
                    for (j in data['median']) {
                       marks_median.push({x:j, y:data['median'][j].M});
                     }
                    for (i in data['raw']) {
                       name.push("");
                       marks_ec.push({x:i, y:data['raw'][i].EC});
                       marks_tds.push({x:i, y:data['raw'][i].TDS});
                     }
                     var step = Math.trunc(i/j);
                       
                     var areaOptionsEC = {
                      events: ['click'],
                       responsive: true,
                       events: ['click'],
                     
                       tooltips: {
                          mode: 'nearest',
                          intersect: true,
                       },          
                       scales: { 
                         yAxes: [{gridLines: {color: "rgba(204, 204, 204,0.1)"}}],
                         xAxes: [{
                              gridLines: {
                                  display: false,
                                  color: "rgba(204, 204, 204,0.1)"
                              }
                            }, {
                              id: 'x-axis-2',
                              type: 'linear',
                              position: 'bottom',
                              display: false,
                              ticks: {
                                min: 0,
                                max: (marks_median.length - 1),
                              } 
                          }]
                        }
                     }                    
                    var areaDataEC = {
                     labels: name,
                      datasets: [{
                        type: 'line',
                        label: 'EC',
                        data: marks_ec,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255,99,132, 1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'TDS',
                        data: marks_tds,
                        backgroundColor: 'rgba(54, 162, 235, 0.4)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'MEAN',
                        data: marks_median,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        pointRadius: 0,
                        xAxisID: 'x-axis-2',
                        borderWidth: 2,
                        fill: false, // 3: no fill
                      }]
                    };
                    var areaChartECCanvas = $("#areaChartEC").get(0).getContext("2d");
                    var areaChartEC = new Chart(areaChartECCanvas, {
                      type: 'line',
                      data: areaDataEC,
                      options: areaOptionsEC
                    });
                 });
       };
       
       buttonECALL.onclick = function () {
          $.post("php/Chart/ec_data_chart.php",
                {button:4},
                function (data){
                   var name = [];
                   var marks_ec = [];
                   var marks_tds = [];
                   var marks_median = [];
                   var i = 0;
                   var j = 0;
                   for (j in data['median']) {
                     marks_median.push({x:j, y:data['median'][j].M});
                   }
                   for (i in data['raw']) {
                     name.push("");
                     marks_ec.push({x:i, y:data['raw'][i].EC});
                     marks_tds.push({x:i, y:data['raw'][i].TDS});
                   }
                   var step = Math.trunc(i/j);
                   var areaOptionsEC = {
                     events: ['click'],
                     responsive: true,
                     
                     tooltips: {
                        mode: 'nearest',
                        intersect: true,
                     },          
                     scales: { 
                       yAxes: [{gridLines: {color: "rgba(204, 204, 204,0.1)"}}],
                       xAxes: [{id: 'B',
                            gridLines: {
                                color: "rgba(204, 204, 204,0.1)"
                            }
                          }, {
                            id: 'x-axis-2',
                            type: 'linear',
                            position: 'bottom',
                            display: false,
                            ticks: {
                                min: 0,
                                max: (marks_median.length - 1),
                              } 
                        }]
                      }
                   }                   
                    var areaDataEC = {
                      labels: name,
                      datasets: [{
                        type: 'line',
                        label: 'EC',
                        data: marks_ec,
                         xAxisID: 'B', 
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255,99,132, 1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'TDS',
                        data: marks_tds,
                         xAxisID: 'B',
                        backgroundColor: 'rgba(54, 162, 235, 0.4)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'MEAN',
                        data: marks_median,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        pointRadius: 0,
                        xAxisID: 'x-axis-2',
                        borderWidth: 2,
                        fill: false, // 3: no fill
                      }]
                   };
                   //areaChart.clear();
                   //areaChart.data = areaDataEC7D;
                   //areaChart.update();
                   var areaChartECCanvas = $("#areaChartEC").get(0).getContext("2d");
                   var areaChartEC = new Chart(areaChartECCanvas, {
                     type: 'line',
                     data: areaDataEC,
                     options: areaOptionsEC
                   });
         });
       };

       
       
       function showGraphEC(){
         {
           $.post("php/Chart/ec_data_chart.php",
                  {button:1},
                  function (data){
                    var name = [];
                    var marks_ec = [];
                    var marks_tds = [];
                    var marks_median = [];
                    var i = 0;
                    var j = 0;
                    for (j in data['median']) {
                       marks_median.push({x:j, y:data['median'][j].M});
                     }
                    for (i in data['raw']) {
                       name.push("");
                       marks_ec.push({x:i, y:data['raw'][i].EC});
                       marks_tds.push({x:i, y:data['raw'][i].TDS});
                     }
                     var step = Math.trunc(i/j);
                       
                     var areaOptionsEC = {
                       responsive: true,
                       events: ['click'],
                       
                       tooltips: {
                          mode: 'nearest',
                          intersect: true,
                       },          
                       scales: { 
                         yAxes: [{gridLines: {color: "rgba(204, 204, 204,0.1)"}}],
                         xAxes: [{
                              gridLines: {
                                  display: false,
                                  color: "rgba(204, 204, 204,0.1)"
                              }
                            }, {
                              id: 'x-axis-2',
                              type: 'linear',
                              position: 'bottom',
                              display: false,
                              ticks: {
                                min: 0,
                                max: (marks_median.length - 1),
                              } 
                          }]
                        }
                     }                    
                    var areaDataEC = {
                     labels: name,
                      datasets: [{
                        type: 'line',
                        label: 'EC',
                        data: marks_ec,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255,99,132, 1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'TDS',
                        data: marks_tds,
                        backgroundColor: 'rgba(54, 162, 235, 0.4)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: true, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'MEAN',
                        data: marks_median,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        pointRadius: 0,
                        xAxisID: 'x-axis-2',
                        borderWidth: 2,
                        fill: false, // 3: no fill
                      }]
                    };
                    var areaChartECCanvas = $("#areaChartEC").get(0).getContext("2d");
                    var areaChartEC = new Chart(areaChartECCanvas, {
                      type: 'line',
                      data: areaDataEC,
                      options: areaOptionsEC
                    });
                 });
         }
       }        
})(jQuery);