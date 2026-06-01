(function($) {
  'use strict';
 
  $(document).ready(function () {
    showGraphT();        
  });

  var areaOptionsT = {
         events: ['click'],
         
         plugins: {  filler: {propagate: true}},
         scales: { 
                    yAxes: [{gridLines: {color: "rgba(204, 204, 204,0.1)"}}],
                    xAxes: [{gridLines: {color: "rgba(204, 204, 204,0.1)"}}]
                 }
       }
       var buttonT7D = document.getElementById('changeT7D');
       var buttonT1M = document.getElementById('changeT1M');
       var buttonT2M = document.getElementById('changeT2M');
       var buttonTALL = document.getElementById('changeTALL');
  
       buttonT7D.onclick = function () {
         $.post("t_data_chart.php",
                {button:7},
                function (data){
                   var name = [];
                   var marks = [];
                   for (var i in data) {
                     name.push("");
                     marks.push(data[i].temperature);
                   }
                   var areaDataT = {
                     labels: name,
                     datasets: [{
                       label: '# Temperature',
                       data: marks,
                       backgroundColor: 'rgb(54, 162, 235, 0.3)',
                       borderColor: 'rgb(54, 162, 235, 1)',
                       borderWidth: 1,
                       pointRadius: 0,
                       fill: true, // 3: no fill
                     }]
                   };
                   //areaChart.clear();
                   //areaChart.data = areaDataT7D;
                   //areaChart.update();
                   var areaChartTCanvas = $("#areaChartT").get(0).getContext("2d");
                   var areaChartT = new Chart(areaChartTCanvas, {
                     type: 'line',
                     data: areaDataT,
                     options: areaOptionsT
                   });
         });
       };
       
       buttonT1M.onclick = function () {
         $.post("t_data_chart.php",
                {button:1},
                function (data){
                   var name = [];
                   var marks = [];
                   for (var i in data) {
                     name.push("");
                     marks.push(data[i].temperature);
                   }
                   var areaDataT = {
                     labels: name,
                     datasets: [{
                       label: '# Temperature',
                       data: marks,
                       backgroundColor: 'rgb(54, 162, 235, 0.3)',
                       borderColor: 'rgb(54, 162, 235, 1)',
                       borderWidth: 1,
                       pointRadius: 0,
                       fill: true, // 3: no fill
                     }]
                   };
                   //areaChart.clear();
                   //areaChart.data = areaDataT7D;
                   //areaChart.update();
                   var areaChartTCanvas = $("#areaChartT").get(0).getContext("2d");
                   var areaChartT = new Chart(areaChartTCanvas, {
                     type: 'line',
                     data: areaDataT,
                     options: areaOptionsT
                   });
         });
       };
       
       buttonT2M.onclick = function () {
          $.post("t_data_chart.php",
                {button:2},
                function (data){
                   var name = [];
                   var marks = [];
                   for (var i in data) {
                     name.push("");
                     marks.push(data[i].temperature);
                   }
                   var areaDataT = {
                     labels: name,
                     datasets: [{
                       label: '# Temperature',
                       data: marks,
                       backgroundColor: 'rgb(54, 162, 235, 0.3)',
                       borderColor: 'rgb(54, 162, 235, 1)',
                       borderWidth: 1,
                       pointRadius: 0,
                       fill: true, // 3: no fill
                     }]
                   };
                   //areaChart.clear();
                   //areaChart.data = areaDataEC7D;
                   //areaChart.update();
                   var areaChartTCanvas = $("#areaChartT").get(0).getContext("2d");
                   var areaChartT = new Chart(areaChartTCanvas, {
                     type: 'line',
                     data: areaDataT,
                     options: areaOptionsT
                   });
         });
       };
       
       buttonTALL.onclick = function () {
          $.post("t_data_chart.php",
                {button:4},
                function (data){
                   var name = [];
                   var marks = [];
                   for (var i in data) {
                     name.push("");
                     marks.push(data[i].temperature);
                   }
                   var areaDataT = {
                     labels: name,
                     datasets: [{
                       label: '# Temperature',
                       data: marks,
                       backgroundColor: 'rgb(54, 162, 235, 0.3)',
                       borderColor: 'rgb(54, 162, 235, 1)',
                       borderWidth: 1,
                       pointRadius: 0,
                       fill: true, // 3: no fill
                     }]
                   };
                   //areaChart.clear();
                   //areaChart.data = areaDataEC7D;
                   //areaChart.update();
                   var areaChartTCanvas = $("#areaChartT").get(0).getContext("2d");
                   var areaChartT = new Chart(areaChartTCanvas, {
                     type: 'line',
                     data: areaDataT,
                     options: areaOptionsT
                   });
         });
       };

  
  function showGraphT(){
    {  
       $.post("t_data_chart.php",
       {button:'1'},
       function (data){
         var name = [];
         var marks = [];
         for (var i in data) {
           name.push("");
           marks.push(data[i].temperature);
         }

         var areaDataT = {
           labels: name,
           datasets: [{
             label: '# Temperature',
             data: marks,
             backgroundColor: 'rgb(54, 162, 235, 0.3)',
             borderColor: 'rgb(54, 162, 235, 1)',
             borderWidth: 1,
             pointRadius: 0,
             fill: true, // 3: no fill
           }]
         };
          
         var areaChartTCanvas = $("#areaChartT").get(0).getContext("2d");
         var areaChartT = new Chart(areaChartTCanvas, {
           type: 'line',
           data: areaDataT,
           options: areaOptionsT
         });
       });
    }
  }  
})(jQuery);