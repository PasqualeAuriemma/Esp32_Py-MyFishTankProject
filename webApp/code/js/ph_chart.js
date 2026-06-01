(function($) {
  'use strict';
    
  $(document).ready(function () {
    showGraph();
    $("#table_ph_history").DataTable({
                "bInfo": true,
                "bJQueryUI": true,
                "bProcessing": true,
                "bPaginate": true,
                "iDisplayLength": 4,
                "sPaginationType": "two_button",
                "sDom": 'rtip',
                'ajax': {
                      'url':'fetch_quantities.php',
                      'type':'post',
                    },
            });
    	
  });
  
  var areaOptionsPH = {
    events: ['click'],
   
    plugins: { filler: {propagate: true}},
    scales: { 
              yAxes: [{gridLines: {color: "rgba(204, 204, 204,0.1)"}}],
              xAxes: [{gridLines: {color: "rgba(204, 204, 204,0.1)"}}]
            }
  }
        
  var buttonPH7D = document.getElementById('changePH7D');
  var buttonPH1M = document.getElementById('changePH1M');
  var buttonPH2M = document.getElementById('changePH2M');
  var buttonPHALL = document.getElementById('changePHALL');
  
  buttonPH7D.onclick = function () {
         $.post("php/Chart/ph_data_chart.php",
                {button:7},
                function (data){
                   var name = [];
                   var marks = [];
                   for (var i in data) {
                     name.push("");
                     marks.push(data[i].ph);
                   }
                   var areaDataPH = {
                     labels: name,
                     datasets: [{
                       label: '# PH',
                       data: marks,
                       backgroundColor: 'rgb(54, 162, 235, 0.3)',
                       borderColor: 'rgb(54, 162, 235, 1)',
                       borderWidth: 1,
                       pointRadius: 0,
                       fill: true, // 3: no fill
                     }]
                   };
                   //areaChartPH.clear();
                   //areaChartPH.data = areaDataPH;
                   //areaChartPH.update();
                   var areaChartCanvas = $("#areaChartPH").get(0).getContext("2d");
                   var areaChartPH = new Chart(areaChartCanvas, {
                     type: 'line',
                     data: areaDataPH,
                     options: areaOptionsPH
                   });
         });
       };
       
    buttonPH1M.onclick = function () {
         $.post("php/Chart/ph_data_chart.php",
                {button:1},
                function (data){
                   var name = [];
                   var marks = [];
                   for (var i in data) {
                     name.push("");
                     marks.push(data[i].ph);
                   }
                   var areaDataPH = {
                     labels: name,
                     datasets: [{
                       label: '# PH',
                       data: marks,
                       backgroundColor: 'rgb(54, 162, 235, 0.3)',
                       borderColor: 'rgb(54, 162, 235, 1)',
                       borderWidth: 1,
                       pointRadius: 0,
                       fill: true, // 3: no fill
                     }]
                   };
                   //areaChartPH.clear();
                   //areaChartPH.data = areaDataPH;
                   //areaChartPH.update();
                   var areaChartCanvas = $("#areaChartPH").get(0).getContext("2d");
                   var areaChartPH = new Chart(areaChartCanvas, {
                     type: 'line',
                     data: areaDataPH,
                     options: areaOptionsPH
                   });
         });
       };
       
  buttonPH2M.onclick = function () {
          $.post("php/Chart/ph_data_chart.php",
                {button:2},
                function (data){
                   var name = [];
                   var marks = [];
                   for (var i in data) {
                     name.push("");
                     marks.push(data[i].ph);
                   }
                   var areaDataPH = {
                     labels: name,
                     datasets: [{
                       label: '# PH',
                       data: marks,
                       backgroundColor: 'rgb(54, 162, 235, 0.3)',
                       borderColor: 'rgb(54, 162, 235, 1)',
                       borderWidth: 1,
                       pointRadius: 0,
                       fill: true, // 3: no fill
                     }]
                   };
                   //areaChartPH.clear();
                   //areaChartPH.data = areaDataPH;
                   //areaChartPH.update();
                   var areaChartCanvas = $("#areaChartPH").get(0).getContext("2d");
                   var areaChartPH = new Chart(areaChartCanvas, {
                     type: 'line',
                     data: areaDataPH,
                     options: areaOptionsPH
                   });
         });
       };
       
  buttonPHALL.onclick = function () {
          $.post("php/Chart/ph_data_chart.php",
                {button:4},
                function (data){
                   var name = [];
                   var marks = [];
                   for (var i in data) {
                     name.push("");
                     marks.push(data[i].ph);
                   }
                   var areaDataPH = {
                     labels: name,
                     datasets: [{
                       label: '# PH',
                       data: marks,
                       backgroundColor: 'rgb(54, 162, 235, 0.3)',
                       borderColor: 'rgb(54, 162, 235, 1)',
                       borderWidth: 1,
                       pointRadius: 0,
                       fill: true, // 3: no fill
                     }]
                   };
                   
                   //areaChartPH.clear();
                   //areaChartPH.data = areaDataPH;
                   //areaChartPH.update();
                   var areaChartCanvas = $("#areaChartPH").get(0).getContext("2d");
                   var areaChartPH = new Chart(areaChartCanvas, {
                     type: 'line',
                     data: areaDataPH,
                     options: areaOptionsPH
                   });
         });
       };
  

    function showGraph(){
    {  
       $.post("php/Chart/ph_data_chart.php",
       {button:'1'},
       function (data){
         var name = [];
         var marks = [];
         for (var i in data) {
           name.push("");
           marks.push(data[i].ph);
         }

         var areaDataPH =  {
           labels: name,
           datasets: [{
             label: '# PH',
             data: marks,
             backgroundColor: 'rgb(54, 162, 235, 0.3)',
             borderColor: 'rgb(54, 162, 235, 1)',
             borderWidth: 1,
             pointRadius: 0,
             fill: true, // 3: no fill
           }]
         };
		 var areaChartCanvas = $("#areaChartPH").get(0).getContext("2d");
         var areaChartPH = new Chart(areaChartCanvas, {
           type: 'line',
           data: areaDataPH,
           options: areaOptionsPH
         });
       });
    }
  }  

})(jQuery);