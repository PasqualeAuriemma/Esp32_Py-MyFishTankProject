<!DOCTYPE html>
<html>
<head>
<title>PIA12 FISH TANK - AQUARIUM</title>
        <link rel="icon" type="image/png" href="/images/salmon.png">
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main16.css" />
     
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body class="">
<div role="navigation" class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a href="https://www.webdamn.com" class="navbar-brand">WEBDAMN.COM</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="https://www.webdamn.com">Home</a></li>
           
          </ul>
         
        </div><!--/.nav-collapse -->
      </div>
    </div>
	
	<div class="container" style="min-height:500px;">
    <div class="container contact">	
	<h2>Example: Datatables Add Edit Delete with Ajax, PHP & MySQL</h2>	
	<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">   		
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-10">
					<h3 class="panel-title"></h3>
				</div>
				<div class="col-md-2" align="right">
					<button type="button" name="add" id="addEmployee" class="btn btn-success btn-xs">Add Employee</button>
				</div>
			</div>
		</div>
		<table id="employeeList" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Age</th>					
					<th>Skills</th>
					<th>Address</th>
					<th>Designation</th>					
					<th></th>
					<th></th>					
				</tr>
			</thead>
		</table>
	</div>
	<div id="employeeModal" class="modal fade">
    	<div class="modal-dialog">
    		<form method="post" id="employeeForm">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Edit User</h4>
    				</div>
    				<div class="modal-body">
						<div class="form-group">
							<label for="name" class="control-label">Name</label>
							<input type="text" class="form-control" id="empName" name="empName" placeholder="Name" required>			
						</div>
						<div class="form-group">
							<label for="age" class="control-label">Age</label>							
							<input type="number" class="form-control" id="empAge" name="empAge" placeholder="Age">							
						</div>	   	
						<div class="form-group">
							<label for="lastname" class="control-label">Skills</label>							
							<input type="text" class="form-control"  id="empSkills" name="empSkills" placeholder="Skills" required>							
						</div>	 
						<div class="form-group">
							<label for="address" class="control-label">Address</label>							
							<textarea class="form-control" rows="5" id="address" name="address"></textarea>							
						</div>
						<div class="form-group">
							<label for="lastname" class="control-label">Designation</label>							
							<input type="text" class="form-control" id="designation" name="designation" placeholder="Designation">			
						</div>						
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="empId" id="empId" />
    					<input type="hidden" name="action" id="action" value="" />
    					<input type="submit" name="save" id="save" class="btn btn-info" value="Save" />
    					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    				</div>
    			</div>
    		</form>
    	</div>
    </div>
    </div>	
<div class="insert-post-ads1" style="margin-top:20px;">

</div>
</div>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<!-- jQuery -->
    <script src="sito/assets/js/jquery.min.js"></script>
    
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>		
    <script src="js/data.js"></script>	
    
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart', 'gauge']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var dataTemp = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Gradi °', <?php echo $temperature;?>],
        ]);
        
        //var dataTDS = google.visualization.arrayToDataTable([
        //    ['Label', 'Value'],
        //    ['ppm', <?php echo $tds;?>],
        //]);

        var dataEC = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['µS/cm', <?php echo $ec;?>],
        ]);

        var dataPH = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['pH', <?php echo $ph;?>],
        ]);
          
        var w = $(window).width();
        var x = Math.floor(w * 0.3);
        //console.log("width: " + w + ", x = " + x);
        var h = $(window).height();
        var y = Math.floor(h * 0.3);
        //console.log("height: " + h + ", y = " + y);

        var options = {
            yellowFrom: 28, yellowTo: 34,
            redFrom: 34, redTo: 45,
            minorTicks: 5,
            max: 45,
            height: y,
            width: x
        };

         //var optionsTDS = {
         //   yellowFrom: 500, yellowTo: 550,
         //   redFrom: 550, redTo: 650,
         //   minorTicks: 10,
         //   max: 650,
         //   height: y,
         //   width: x
         //}; 
        
        var optionsEC = {
            yellowFrom: 900, yellowTo: 920,
            redFrom: 920, redTo: 1000,
            minorTicks: 10,
            max: 1000,
            height: y,
            width: x
        }; 
        
        var optionsPH = {
            yellowFrom: 0, yellowTo: 6.5,
            greenFrom: 6.5, greenTo: 8,
            redFrom: 8, redTo: 12,
            minorTicks: 0,
            max: 12,
            height: y,
            width: x
        };
          
        function resize() {    
          var chartT = new google.visualization.Gauge(document.getElementById('chart_temp'));
          var chartE = new google.visualization.Gauge(document.getElementById('chart_ec'));
          //var chartS = new google.visualization.Gauge(document.getElementById('chart_tds'));
          var chartP = new google.visualization.Gauge(document.getElementById('chart_ph'));
          chartT.clearChart();
          chartT.draw(dataTemp, options);
          chartE.clearChart();
          chartE.draw(dataEC, optionsEC);
          //chartS.clearChart();
          //chartS.draw(dataTDS, optionsTDS);
          chartP.clearChart();
          chartP.draw(dataPH, optionsPH);
        }
        window.onload = resize();
        window.onresize = resize;
      }
    </script>

</body>
</html>
