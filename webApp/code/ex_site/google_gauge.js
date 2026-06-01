(function($) {
    'use strict';
  $(document).ready(function () {
    showECGauge();
  });
     
  function showECGauge(){
    {
       $.post("get_gauge_value.php",
              {button:3},
              function (data){
               var ec_valuew = 0;
               var ph_value = 0;
               var t_value = 0;
               alert(data[0]);
               for (var j in data) {
                  ec_valuew = j.ValueEC;
                  ph_value = j.ValuePH;
                  t_value = j.ValueT;
               }
               alert(ec_valuew);
               google.charts.load('current', {'packages':['gauge', 'corechart']});
               //google.load('visualization', '1', {packages: ['corechart', 'gauge']});
               google.charts.setOnLoadCallback(drawCharts(508));
               
               function drawCharts(ec_value_to_show) {
                  //var w = $(window).width();
                  //var x = Math.floor(w * 0.23);
                  //console.log("width: " + w + ", x = " + x);
                  //var h = $(window).height();
                  //var y = Math.floor(h * 0.3);
                  //console.log("height: " + h + ", y = " + y);
                  alert(ec_value_to_show);
                  var dataEC = google.visualization.arrayToDataTable([
                    ['Label', 'Value'],
                    ['µS/cm', ec_value_to_show],
                  ]); 
                  
                  var optionsEC = {
                      yellowFrom: 600, yellowTo: 820,
                      redFrom: 820, redTo: 1000,
                      minorTicks: 10, max: 1000,
                      height: y, width: x
                  };   
                  
                  var chartE = new google.visualization.Gauge(document.getElementById('chart_ec'));
                  chartE.clearChart();
				  chartE.draw(dataEC, optionsEC); 
                          
               }
       }); 
   }}
      
})(jQuery);