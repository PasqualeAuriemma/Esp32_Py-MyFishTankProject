<!DOCTYPE html>
<!--
	Escape Velocity by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>

 
	    <title>PIA12 FISH TANK - AQUARIUM</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="icon" type="image/png" href="/images/salmon.png">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<!-- jQuery -->
		<script src="js/jquery.dataTables.min.js"></script>
		<script src="js/dataTables.bootstrap.min.js"></script>
		<script src="sito/assets/js/util.js"></script>
		<script src="sito/assets/js/jquery.dropotron.min.js"></script>
		<script src="sito/assets/js/browser.min.js"></script>
		<script src="sito/assets/js/breakpoints.min.js"></script>
		<!-- <script src="sito/assets/js/main.js"></script> -->
		<!-- oppure <script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script> -->
		<script src="sito/assets\js\flickity.pkgd.min.js"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<link rel="stylesheet" href="assets/css/main16.css" />
        <link rel="stylesheet" href="dataTables.bootstrap.min.css" />
	    <!-- Bootstrap CSS -->
  <link href="css/bootstrap5.0.1.min.css" rel="stylesheet"  crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="css/datatables-1.10.25.min.css"/>
  
  <style type="text/css">
    .btnAdd {
      text-align: right;
      width: auto;
      margin-bottom: 20px;
    }
  </style>
    
    <script src="js/data.js"></script>	
		<?php
			include("connection1.php");
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			$sqlT = "SELECT temperature, data_send as send FROM my_myfishtank.temp_tab ORDER BY data_send DESC limit 1";
			$sqlEC = "SELECT ec, data_send as send FROM my_myfishtank.ec_tab ORDER BY data_send DESC limit 1";
			$sqlTDS = "SELECT tds, data_send as send FROM my_myfishtank.tds_tab ORDER BY data_send DESC limit 1";
			$sqlPH = "SELECT ph, data_send as send FROM my_myfishtank.ph_tab ORDER BY data_send DESC limit 1";
          
			$resultT = $conn->query($sqlT);
		    $resultEC = $conn->query($sqlEC);
		    $resultTDS = $conn->query($sqlTDS);
		    $resultPH = $conn->query($sqlPH);
		  
		    if ($resultT->num_rows > 0) {
			// output data of each row
			  while($rowT = $resultT->fetch_assoc()) {
			    $temperature = $rowT["temperature"];
			    $sendT = $rowT["send"];
			  }
		    } else {
				$temperature = 0;
				$sendT = "no data";
		    }
      
			if ($resultEC->num_rows > 0) {
			// output data of each row
				while($rowEC = $resultEC->fetch_assoc()) {
					$ec = $rowEC["ec"];
					$sendEC = $rowEC["send"];
				}
			} else {
				$ec = 0;
				$sendEC = "no data";
			}
      
			if ($resultTDS->num_rows > 0) {
				// output data of each row
				while($rowTDS = $resultTDS -> fetch_assoc()) {
				$tds = $rowTDS["tds"];
				$sendTDS = $rowTDS["send"];
				}
			} else {
				$tds = 0;
				$sendTDS = "no data";
			}
      
			if ($resultPH->num_rows > 0) {
				// output data of each row
				while($rowPH = $resultPH->fetch_assoc()) {
					$ph = $rowPH["ph"];
					$sendPH = $rowPH["send"];
				}
			} else {
				$ph = 0;
				$sendPH = "no data";
			}
		$conn->close();
    ?>
</head>
<body class="homepage is-preload">
	<div id="page-wrapper">
      <!-- Header -->
      <section id="header" class="wrapper">
        <!-- Logo -->
        <div id="logo">
            <h1><a href="index.php">AQUARIUM PIA12</a></h1>
            <p>The easy way to manage your fish tank</p>
        </div>
        <!-- Nav -->
        <!--<nav id="nav">
          <ul>
            <li class="current"><a href="index.html">Home</a></li>
            <li>
              <a href="#">Measurements</a>
                <ul>
                  <li><a href="temperature.php">Temperature</a></li>
                  <li><a href="ph.txt">PH</a></li>
                  <li><a href="ec.php">EC</a></li>
                  <li><a href="tds.php">TDS</a></li>
                </ul>
              </li>
            <li><a href="left-sidebar.html">Left Sidebar</a></li>
            <li><a href="right-sidebar.html">Right Sidebar</a></li>
            <li><a href="no-sidebar.html">Contatti</a></li>
          </ul>
        </nav>-->
      </section>

      <!-- Intro -->
      <section id="intro" class="wrapper-carousel style1">
        <div class="container">
          <div class="carousel" data-flickity='{ "wrapAround": true, "autoPlay": true, "imagesLoaded":true }'>
              <div class="carousel-cell">
                  <img class="w3-image" src="images\acquarium.jpg">
              </div>
              <div class="carousel-cell">
                  <img class="w3-image" src="images\acquarium1.jpg">
              </div>
              <div class="carousel-cell">
                  <img class="w3-image" src="images\acquarium2.jpg">
              </div>
              <div class="carousel-cell">
                  <img class="w3-image" src="images\acquarium3.jpg">
              </div>
              <div class="carousel-cell">
                  <img class="w3-image" src="images\acquarium4.jpg">
              </div>
          </div>
        </div>
      </section>
      <!-- DashBoard -->        
      <section id="highlights" class="wrapper style5">
        <div class="title">Temperature</div>
        <div class="container">
          <div class="row aln-center">
            <div class="col-4 col-12-medium">
               <center><p class="style4"> Recorded on <?php echo gmdate("l jS \of F Y", $sendT). " at ".gmdate("H:i:s", $sendT);?></p></center>
                <div class="container_chart">
                  <div class="row_chart">        
			        <div id="chart_temp"></div>
                  </div>
                </div>    
                <center><ul class="actions">
                  <li><a href="temperature.php" class="button style3">More Details</a></li>
                </ul></center>
            </div>
            <div class="col-4 col-12-medium">
              <center>
                <center><p class="style4"> Recorded on <?php echo gmdate("l jS \of F Y", $sendEC). " at ".gmdate("H:i:s", $sendEC);?></p></center>
              </center>  
              <div class="container_chart">
                <div class="row_chart">
                  <div id="chart_ec"></div>
                </div>
               </div>	
               <center>
                <ul class="actions" >
                  <li><a href="tds_ec.php" class="button style3">More Details</a></li>
                </ul>
               </center>   
            </div>
            <div class="col-4 col-12-medium">
              <center>
                  <center><p class="style4"> Recorded on <?php echo gmdate("l jS \of F Y", $sendPH). " at ".gmdate("H:i:s", $sendPH);?></p></center>
               </center>  
			    <div class="container_chart">
                  <div class="row_chart">
                    <div id="chart_ph"></div>
                  </div>
                </div>
                <center>
                  <ul class="actions">
                    <li><a href="ph.php" class="button style3">More Details</a></li>
                  </ul>
			    </center>
            </div>
		  </div>     
        </div>
    </section>
    <!-- Main -->
    <section id="main" class="wrapper style3">
        <div class="title">Water values</div>
		<div class="container" style="min-height:500px;">
    <div class="container contact">	
	<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">   		
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-10">
					<h3 class="panel-title"></h3>
				</div>
				<div class="col-md-2" align="right">
					<button type="button" name="add" id="addEmployee" class="btn btn-success btn-xs">Add Values</button>
				</div>
			</div>
		</div>
		<table id="employeeList" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Data</th>
					<th>Nitriti</th>					
					<th>Nitrati</th>
					<th>GH</th>
					<th>KH</th>					
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
    					<button7 type="button" class="close" data-dismiss="modal">&times;</button7>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Edit Values</h4>
    				</div>
    				<div class="modal-body">
                        <div class="form-group">
							<label for="secure" class="control-label">Secure</label>
							<input type="text" class="form-control" id="empSec" name="empSec" placeholder="" required>			
						</div>
						<div class="form-group">
							<label for="age" class="control-label">Nitriti</label>							
							<input type="number" class="form-control" id="empAge" name="empAge" placeholder="No2">							
						</div>	   	
						<div class="form-group">
							<label for="lastname" class="control-label">Nitrati</label>							
							<input type="number" class="form-control"  id="empSkills" name="empSkills" placeholder="No3" required>							
						</div>	 
						<div class="form-group">
							<label for="address" class="control-label">GH</label>							
							<input type="number" class="form-control"  id="address" name="address" placeholder="GH">							
						</div>
						<div class="form-group">
							<label for="lastname" class="control-label">KH</label>							
							<input type="number" class="form-control" id="designation" name="designation" placeholder="KH">			
						</div>						
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="empId" id="empId" />
    					<input type="hidden" name="action" id="action" value="" />
    					<input type="submit" name="save" id="save" class="btn btn-info" value="Save" />
    					<button9 type="button" class="btn btn-default" data-dismiss="modal">Close</button9>
    				</div>
    			</div>
    		</form>
    	</div>
    </div>
    </div>	
<div class="insert-post-ads1" style="margin-top:20px;">

</div>
</div>
    </section> 
    <!-- Main -->
    <section id="main" class="wrapper style4">
        <div class="title">Fertilization</div>
        <div class="container">
        <?php include('connection.php');?>
        <div class="container-fluid">
    <h2 class="text-center">Welcome to Datatable</h2>
    <p class="datatable design text-center">Welcome to Datatable</p>
   <div class="row">
   
      <div class="container">
        <div class="btnAdd">
		
         <a href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addUserModal"   class="btn btn-success btn-sm" >Add User</a>
       </div>
	   
       <div class="row">
	   
        <div class="col-md-2"></div>
        <div class="col-md-8">
		
         <table id="example" class="table">
          <thead>
            <th>Id</th>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>City</th>
            <th>Options</th>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <div class="col-md-2"></div>
    </div>
	
  </div>
  
  
  </div>
</div>
         
         
         
        </div>
    </section>        
    <!-- Footer -->
	<section id="footer" class="wrapper">
		<div class="container">
			<div class="row">
				<div class="col-6 col-12-medium">
					<!-- Contact -->
					<section class="feature-list small">
						<div class="row">
							<div class="col-6 col-12-small">
								<section>
									<h3 class="icon solid fa-comment">Social</h3>
										<p>
										<a href="https://github.com/PasqualeAuriemma/MyFishTankProject">Github</a><br />
										<a href="https://www.linkedin.com/in/pasquale-auriemma-780953b8/">LinkedIn</a><br />
										<a href="https://it.altervista.org/">Altervista</a>
										</p>
								</section>
							</div>
							<div class="col-6 col-12-small">
								<section>
									<h3 class="icon solid fa-envelope">Email</h3>
									<p>
										<a href="#">info@untitled.tld</a>
									</p>
								</section>
							</div>
						</div>
					</section>
				</div>
			</div>
			<div id="copyright">
				<a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img alt="Licenza Creative Commons" style="border-width:0" src="https://i.creativecommons.org/l/by/4.0/88x31.png" /></a>
				<br />Quest'opera è distribuita con Licenza <a rel="license" href="http://creativecommons.org/licenses/by/4.0/">Creative Commons Attribuzione 4.0 Internazionale</a>.
			</div>
		</div>
	</section>
</div>
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
</body></html>

<script type="text/javascript">
  //var table = $('#example').DataTable();
</script>