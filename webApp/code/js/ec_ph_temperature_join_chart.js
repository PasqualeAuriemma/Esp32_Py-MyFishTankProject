(function($) {
    'use strict';
  $(document).ready(function () {
    showGraphJoin();
  });

 var buttonJoin7D = document.getElementById('changeJoin7D');
 var buttonJoin1M = document.getElementById('changeJoin1M');
 var buttonJoin2M = document.getElementById('changeJoin2M');
 var buttonJoinALL = document.getElementById('changeJoinALL');

// JavaScript
function normalize(list) {
   var minMax = list.reduce((acc, value) => {
      if (value.y < acc.min) {
         acc.min = value.y;
      }
      if (value.y > acc.max) {
         acc.max = value.y;
      }
      return acc;
   }, {min: Number.POSITIVE_INFINITY, max: Number.NEGATIVE_INFINITY});
     
   return list.map(value => {
      // Verify that you're not about to divide by zero
      if (minMax.max === minMax.min) {
         return 1 / list.length
      }
      var diff = minMax.max - minMax.min;
      var ii = ((value.y - minMax.min) / diff).toFixed(2);
     // console.log(ii);
      return ii;
   });
}

buttonJoin7D.onclick = function () {
      $.post("php/Chart/ec_ph_temp_data_chart.php",
             {button:7},
             function (data){             
               var name = [];
               var marks_ec = [];
               var marks_ph = [];
               var marks_temp = [];
               var i = 0;
               var j = 0;
               var z = 0;
               for (j in data['temp']) {
                 marks_temp.push({x:j, y:data['temp'][j].M_T});
               }
               for (z in data['ph']) {
                 marks_ph.push({x:z, y:data['ph'][z].M_PH});
               }
               
               for (i in data['ec']) {
                 name.push("");
                 marks_ec.push({x:i, y:data['ec'][i].M});
               }
               var step = Math.trunc(i/j);
         
               var areaOptionsJoin = {
                 events: ['click'],
                 responsive: true,
                 tooltips: {
                    mode: 'nearest',
                    intersect: true,
                 },          
                 scales: { 
                   yAxes: [{gridLines: {color: "rgba(204, 204, 204,0.1)"}}],
                   xAxes: [{id: 'B', gridLines: {color: "rgba(204, 204, 204,0.1)"}},
                           {id: 'x-axis-2', type: 'linear', position: 'bottom', display: false,
                            ticks: {min: 0, max: (marks_ph.length - 1),}},
                            {id: 'x-axis-3', type: 'linear', position: 'bottom', display: false,
                            ticks: {min: 0, max: (marks_temp.length - 1),}
                          }]
                  }
               }
               var areaDataJoin = {
                      labels: name,
                      datasets: [{
                        type: 'line',
                        label: 'EC',
                        data: normalize(marks_ec),
                        xAxisID: 'B',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255,99,132, 1)',
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: false, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'pH',
                        data: normalize(marks_ph),
                        backgroundColor: 'rgba(54, 162, 235, 0.4)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        xAxisID: 'B',
                        pointRadius: 0,
                        fill: false, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'Temperature',
                        data: normalize(marks_temp),
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        pointRadius: 0,
                        xAxisID: 'B',
                        borderWidth: 2,
                        fill: false, // 3: no fill
                    }]
                   };
                   //areaChart.clear();
                   //areaChart.data = areaDataT7D;
                   //areaChart.update();
                   var areaChartJoinCanvas = $("#areaChartJoin").get(0).getContext("2d");
                   var areaChartJoin = new Chart(areaChartJoinCanvas, {
                     type: 'line',
                     data: areaDataJoin,
                     options: areaOptionsJoin
                   });
         });
       };
       
buttonJoin1M.onclick = function () {
         $.post("php/Chart/ec_ph_temp_data_chart.php",
                {button:1},
                function (data){
                   var name = [];
                   var marks_ec = [];
                   var marks_ph = [];
                   var marks_temp = [];
                   var i = 0;
                   var j = 0;
                   var z = 0;
                   for (j in data['temp']) {
                     marks_temp.push({x:j, y:data['temp'][j].M_T});
                   }
                   for (z in data['ph']) {
                     marks_ph.push({x:z, y:data['ph'][z].M_PH});
                   }
                   for (i in data['ec']) {
                     name.push("");
                     marks_ec.push({x:i, y:data['ec'][i].M});
                   }
                   var step = Math.trunc(i/j);            
               
                   var areaOptionsJoin = {
                     events: ['click'],
                     responsive: true,
                     tooltips: {mode: 'nearest',
                                intersect: true,
                     },          
                     scales: {yAxes: [{gridLines: {color: "rgba(204, 204, 204,0.1)"}}],
                              xAxes: [{id: 'B',gridLines: {color: "rgba(204, 204, 204,0.1)"}
                                     }]
                              }
                   }
                   var areaDataJoin = {
                      labels:name,
                      datasets: [{
                        type: 'line',
                        label: 'EC',
                        xAxisID: 'B',
                        data: normalize(marks_ec),
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255,99,132, 1)',
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: false, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'pH',
                        data: normalize(marks_ph),
                        backgroundColor: 'rgba(54, 162, 235, 0.4)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        xAxisID: 'B',
                        pointRadius: 0,
                        fill: false, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'Temperature',
                        data: normalize(marks_temp),
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        pointRadius: 0,
                        xAxisID: 'B',
                        borderWidth: 2,
                        fill: false, // 3: no fill
                      }]
                   };
                   //areaChart.clear();
                   //areaChart.data = areaDataT7D;
                   //areaChart.update();
                   var areaChartJoinCanvas = $("#areaChartJoin").get(0).getContext("2d");
                   var areaChartJoin = new Chart(areaChartJoinCanvas, {
                     type: 'line',
                     data: areaDataJoin,
                     options: areaOptionsJoin
                   });
         });
};
       
buttonJoin2M.onclick = function () {
          $.post("php/Chart/ec_ph_temp_data_chart.php",
                  {button:2},
                  function (data){
                    var name = [];
                    var marks_ec = [];
                    var marks_ph = [];
                    var marks_temp = [];
                    var i = 0;
                    var j = 0;
                    var z = 0;
                    for (j in data['temp']) {
                      marks_temp.push({x:j, y:data['temp'][j].M_T});
                    }
                    for (z in data['ph']) {
                      marks_ph.push({x:z, y:data['ph'][z].M_PH});
                    }
                    for (i in data['ec']) {
                      name.push("");
                      marks_ec.push({x:i, y:data['ec'][i].M});
                    }
                    var step = Math.trunc(i/j);   
                    var areaOptionsJoin = {
                      events: ['click'],
                      responsive: true,
                      events: ['click'],
                      tooltips: {mode: 'nearest',
                                 intersect: true,
                      },          
                      scales: {yAxes: [{gridLines: {color: "rgba(204, 204, 204,0.1)"}}],
                               xAxes: [{id: 'B',gridLines: {display: false, color: "rgba(204, 204, 204,0.1)"}
                                      }]
                              }
                    }                    
                    var areaDataJoin = {
                     labels: name,
                      datasets: [{
                        type: 'line',
                        label: 'EC',
                        xAxisID: 'B',
                        data: normalize(marks_ec),
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255,99,132, 1)',
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: false, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'pH',
                        data: normalize(marks_ph),
                        backgroundColor: 'rgba(54, 162, 235, 0.4)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        xAxisID: 'B',
                        pointRadius: 0,
                        fill: false, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'Temperature',
                        data: normalize(marks_temp),
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        pointRadius: 0,
                        xAxisID: 'B',
                        borderWidth: 2,
                        fill: false, // 3: no fill
                      }]
                    };
                    var areaChartJoinCanvas = $("#areaChartJoin").get(0).getContext("2d");
                    var areaChartJoin = new Chart(areaChartJoinCanvas, {
                      type: 'line',
                      data: areaDataJoin,
                      options: areaOptionsJoin
                    });
                 });
       };
       
buttonJoinALL.onclick = function () {
          $.post("php/Chart/ec_ph_temp_data_chart.php",
                {button:4},
                function (data){
                   var name = [];
                   var marks_ec = [];
                   var marks_ph = [];
                   var marks_temp = [];
                   var i = 0;
                   var j = 0;
                   var z = 0;
                   for (j in data['temp']) {
                     marks_temp.push({x:j, y:data['temp'][j].M_T});
                   }
                   for (z in data['ph']) {
                     marks_ph.push({x:z, y:data['ph'][z].M_P});
                   }
                   for (i in data['ec']) {
                     name.push("");
                     marks_ec.push({x:i, y:data['ec'][i].M});
                   }                  
                   var step = Math.trunc(i/j);
                   var areaOptionsJoin = {
                     events: ['click'],
                     responsive: true,
                     tooltips: {mode: 'nearest',
                                intersect: true,
                               },          
                     scales: { 
                       yAxes: [{gridLines: {color: "rgba(204, 204, 204,0.1)"}}],
                       xAxes: [{id: 'B', gridLines: {color: "rgba(204, 204, 204,0.1)"}
                              }]
                     }
                   }                   
                   var areaDataJoin = {
                      labels: name,
                      datasets: [{
                        type: 'line',
                        label: 'EC',
                        data: normalize(marks_ec),
                        xAxisID: 'B', 
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255,99,132, 1)',
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: false, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'pH',
                        data: normalize(marks_ph),
                        xAxisID: 'B',
                        backgroundColor: 'rgba(54, 162, 235, 0.4)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        xAxisID: 'B',
                        pointRadius: 0,
                        fill: false, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'Temperature',
                        data: normalize(marks_temp),
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        pointRadius: 0,
                        xAxisID: 'B',
                        borderWidth: 2,
                        fill: false, // 3: no fill
                      }]
                   };
                   //areaChart.clear();
                   //areaChart.data = areaDataEC7D;
                   //areaChart.update();
                   var areaChartJoinCanvas = $("#areaChartJoin").get(0).getContext("2d");
                   var areaChartJoin = new Chart(areaChartJoinCanvas, {
                     type: 'line',
                     data: areaDataJoin,
                     options: areaOptionsJoin
                   });
         });
       };
       

function showGraphJoin(){
         {$.post("php/Chart/ec_ph_temp_data_chart.php",
                  {button:1},
                  function (data){
                    var name = [];
                    var marks_ec = [];
                    var marks_ph = [];
                    var marks_temp = [];
                    var i = 0;
                    var j = 0;
                    var z = 0;
                    for (j in data['temp']) {
                      marks_temp.push({x:j, y:data['temp'][j].M_T});
                    }
                    for (z in data['ph']) {
                      marks_ph.push({x:z, y:data['ph'][z].M_PH});
                    }
                    for (i in data['ec']) {
                      name.push("");
                      marks_ec.push({x:i, y:data['ec'][i].M});
                    }  
                    
                    var ii = normalize(marks_ec);
                    var iii = normalize(marks_ph);
                    var iiii = normalize(marks_temp);
                     
                    var step = Math.trunc(i/j);  
                    
                    var areaOptionsJoin = {
                       responsive: true,
                       events: ['click'],
                       
                       tooltips: {
                          mode: 'nearest',
                          intersect: true,
                       },          
                       scales: { 
                         yAxes: [{gridLines: {color: "rgba(204, 204, 204,0.1)"}}],
                         xAxes: [{id: 'B', gridLines: {display: false, color: "rgba(204, 204, 204,0.1)"}
                            }]
                        }
                     }     
                    var areaDataJoin = {
                     labels: name,
                      datasets: [ {
                        type: 'line',
                        label: 'EC',
                        xAxisID: 'B',
                        data: ii,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255,99,132, 1)',
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: false, // 3: no fill
                      },{
                        type: 'line',
                        label: 'pH',
                        data: iii,
                        xAxisID: 'B',
                        backgroundColor: 'rgba(54, 162, 235, 0.4)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: false, // 3: no fill
                      }, {
                        type: 'line',
                        label: 'Temperature',
                        data: iiii,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        pointRadius: 0,
                        borderWidth: 2,
                        xAxisID: 'B',
                        fill: false, // 3: no fill
                      }]
                    };
                    var areaChartJoinCanvas = $("#areaChartJoin").get(0).getContext("2d");
                    var areaChartJoin = new Chart(areaChartJoinCanvas, {
                      type: 'line',
                      data: areaDataJoin,
                      options: areaOptionsJoin
                    });
                 });
         }
       }        
})(jQuery);