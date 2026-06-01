<!DOCTYPE HTML>
<!--
	Escape Velocity by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>PIA12 FISH TANK - AQUARIUM</title>
        <link rel="icon" type="image/png" href="/images/salmon.png">
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main14.css" />
        <link href="assets/css/bootstrap5.0.1.min.css" rel="stylesheet"  crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="assets/css/datatables-1.10.25.min.css"/>
        
        <style type="text/css">
           
           div.dataTables_wrapper {
              width: 100%;
              margin: 0 auto;
           }
           table, th, td {
              border: 1px solid black;
              border-collapse: collapse;
            }
        </style>
        <?php
          include("connection.php");
          if ($con->connect_error) {
            die("Connection failed: " . $con->connect_error);
          }
          $sqlT = "SELECT temperature, data_send as send FROM my_myfishtank.temp_tab ORDER BY data_send DESC limit 1";
          $sqlEC = "SELECT ec, data_send as send FROM my_myfishtank.ec_tab ORDER BY data_send DESC limit 1";
          //$sqlTDS = "SELECT tds, data_send as send FROM my_myfishtank.tds_tab ORDER BY data_send DESC limit 1";
          $sqlPH = "SELECT ph, data_send as send FROM my_myfishtank.ph_tab ORDER BY data_send DESC limit 1";

          $resultT = $con->query($sqlT);
          $resultEC = $con->query($sqlEC);
          //$resultTDS = $conn->query($sqlTDS);
          $resultPH = $con->query($sqlPH);

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

          //if ($resultTDS->num_rows > 0) {
          // output data of each row
          //while($rowTDS = $resultTDS -> fetch_assoc()) {
          //  $tds = $rowTDS["tds"];
          //  $sendTDS = $rowTDS["send"];
          //}
          //} else {
          //$tds = 0;
          //$sendTDS = "no data";
          //}

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

          $con->close();
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
                      <li><a href="#">Measurements</a>
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
                          <img class="w3-image" src="images\acquarium4.jpg">
                      </div>
                      <div class="carousel-cell">
                          <img class="w3-image" src="images\riallestimento0.jpg">
                      </div>
                  </div>
              </div>
          	</section>
          	<!-- DashBoard -->        
          	<section id="highlights" class="wrapper style1">
              <div class="title">Monitoring</div>
              <div class="container"> 
                  <div class="row aln-center">
                      <div class="col-4 col-12-medium">
                          <section class="highlight">
                              <center><p class="style4"> Recorded on <?php echo gmdate("l jS \of F Y", $sendT). " at ".gmdate("H:i:s", $sendT);?></p></center>
                              <div class="container_chart">
                                  <div class="row_chart">        
                                      <div id="chart_temp"></div>
                                  </div>
                              </div>    
                              <center><ul class="actions">
                                  <li><a href="temperature.php" class="button style3">More Details</a></li>
                              </ul></center>		
                          </section>
                      </div>	
                      <div class="col-4 col-12-medium">
                          <section class="highlight">
                              <center> <p class="style4"> Recorded on <?php echo gmdate("l jS \of F Y", $sendEC). " at ".gmdate("H:i:s", $sendEC);?></p></center>
                              <div class="container_chart">
                                  <div class="row_chart">
                                      <div id="chart_ec"></div>
                                  </div>
                              </div>
                              <center><ul class="actions" >
                                  <li><a href="tds_ec.php" class="button style3">More Details</a></li>
                              </ul></center>  
                          </section>
                      </div>
                      <div class="col-4 col-12-medium">
                          <section class="highlight">
                              <center><p class="style4"> Recorded on <?php echo gmdate("l jS \of F Y", $sendPH). " at ".gmdate("H:i:s", $sendPH);?></p></center>
                              <div class="container_chart">
                                  <div class="row_chart">
                                      <div id="chart_ph"></div>
                                  </div>
                              </div>
                              <center><ul class="actions" >
                                  <li><a href="ph.php" class="button style3">More Details</a></li>
                              </ul></center>  
                          </section>
                      </div>
                  </div>    
              </div>
          	</section>
          	<!-- Main -->
          	<section id="main" class="wrapper style5">
			  <div class="title">Water Values</div>
              <div class="container">
        
                <table id="waterValuesTable" class="table" style="width:100%">
                   	<thead>
                       	<th>Data</th>
                        <th>Nitriti</th>
                        <th>Nitrati</th>
                        <th>GH</th>
                        <th>KH</th>
                        <th>Fosfati</th>
                        <th>Options</th>
					</thead>
					<tbody>
					</tbody>
				</table>
                <left>  
              		<a href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addWVModal" class="btn btn-success btn-sm" >Add Values</a>	
               	</left>  
              </div>
          	</section> 
          	<!-- Main -->
          	<section id="main" class="wrapper style4">
              <div class="title">Fertilization Diary</div>
              <div class="container">
                <table id="fertilizationTable" class="table" style="width:100%">
                   	<thead>
                       	<th>Data</th>
                        <th>Potassio ml</th>
                        <th>Magnesio ml</th>
                        <th>Ferro ml</th>
                        <th>Rinverdente ml</th>
                        <th>Fosforo ml</th>
                        <th>Azoto ml</th>
						<th>Stick NPK</th>
                        <th>Options</th>
					</thead>
					<tbody>
					</tbody>
				</table>
                <left>  
              		<a href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addFertilizationModal"   class="btn btn-success btn-sm" >Add Quantities</a>
               	</left>  
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
    	<!-- Optional JavaScript; choose one of the two! -->
		<!-- Option 1: Bootstrap Bundle with Popper -->
		<script src="assets/js/jquery-3.6.0.min.js"  crossorigin="anonymous"></script>
		<script src="assets/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
		<script type="text/javascript" src="assets/js/dt-1.10.25datatables.min.js"></script>
		<!-- Option 2: Separate Popper and Bootstrap JS -->
    	<!--
    	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
  		-->    
        <script type="text/javascript">
        	$(document).ready(function() {
      			$('#fertilizationTable').DataTable({
        			"fnCreatedRow": function( nRow, aData, iDataIndex ) {
          				$(nRow).attr('id', aData[0]);},
                    "scrollX": true,
                    "pageLength": 5,
                    "lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
                    'serverSide':'true',
                    'processing':'true',
                    'paging':'true',
                    'order':[],
                    'ajax': {
                      'url':'Fertilization/fetch_quantities.php',
                      'type':'post',
                    },
                    "columnDefs": [{
                      'target':[5],
                      'orderable' :false,
                    }]
                });
    		});
			$(document).on('submit','#addQuantities',function(e){
      			e.preventDefault();
                var key= $('#addKeyQField').val();
                var k= $('#addKField').val();
                var mg= $('#addMgField').val();
                var fe= $('#addFeField').val();
                var rinverdente = $('#addRinverdenteField').val();
                var p= $('#addPField').val();
                var n= $('#addNField').val();
                var npk= $('#addNPKField').val();
                if (key == "Pia12"){
                       $.ajax({
                         url:"Fertilization/add_quantities.php",
                         type:"post",
                         data:{potassio:k, magnesio:mg, ferro:fe, rinverdente:rinverdente, fosforo:p, azoto:n, npk:npk},
                         success:function(data){
                           var json = JSON.parse(data);
                           var status = json.status;
                           if(status=='true'){
                            mytable =$('#fertilizationTable').DataTable();
                            mytable.draw();
                            $('#addFertilizationModal').modal('hide');
                            var frm = document.getElementsByName('addQuantities')[0];
                            frm.reset();  // Reset all form data
                           }else{
                            alert('failed');
                           }
                         }
                      });
    			}else{
                   
      				alert('Key wrong');
    			}
  			});
			$(document).on('submit','#updateQuantities',function(e){
      			e.preventDefault();
                //var tr = $(this).closest('tr');
                var key= $('#keyQField').val();
                var k= $('#kField').val();
                var mg= $('#mgField').val();
                var fe= $('#feField').val();
                var rinverdente= $('#rinverdenteField').val();
                var p= $('#pField').val();
                var n= $('#nField').val();
                var npk= $('#npkField').val();
                var trid= $('#tridQ').val();
                var id= $('#idQ').val();
                var date= $('#dateQ').val();
                if (key == "Pia12"){
                       $.ajax({
                         url:"Fertilization/update_quantities.php",
                         type:"post",
                         data:{potassio:k, magnesio:mg, ferro:fe, rinverdente:rinverdente, fosforo:p, azoto:n, npk:npk, id:id},
                         success:function(data){
                           var json = JSON.parse(data);
                           var status = json.status;
                           if(status=='true'){
                            table =$('#fertilizationTable').DataTable();
                            // table.cell(parseInt(trid) - 1,0).data(id);
                            // table.cell(parseInt(trid) - 1,1).data(username);
                            // table.cell(parseInt(trid) - 1,2).data(email);
                            // table.cell(parseInt(trid) - 1,3).data(mobile);
                            // table.cell(parseInt(trid) - 1,4).data(city);
                            var button =   '<td><a href="javascript:void();" data-id="' +id + '" class="btn btn-info btn-sm editbtnF">Edit</a>  <a href="#!"  data-id="' +id + '"  class="btn btn-danger btn-sm deleteBtnF">Delete</a></td>';
                            var row = table.row("[id='"+trid+"']");
                             var dateClass = new Date(date)
                            var dateFormat = dateClass.getDate()+"/"+(dateClass.getMonth()+1)+"/"+dateClass.getFullYear()
                            row.row("[id='" + trid + "']").data([dateFormat, k, mg, fe, rinverdente, p, n, npk, button]);
                            $('#updateFertilizationModal').modal('hide');
                           }else{
                            alert('failed');
                           }
                         }
                       });
      			}else{
      				alert('Key wrong');
    			}
    		});
    		$('#fertilizationTable').on('click','.editbtnF ',function(event){
              var table = $('#fertilizationTable').DataTable();
              var trid = $(this).closest('tr').attr('id');
              // console.log(selectedRow);
              var id = $(this).data('id');
              $('#updateFertilizationModal').modal('show');
              $.ajax({
                url:"Fertilization/get_single_quantity.php",
                data:{id:id},
                type:'post',
                success:function(data){
                 var json = JSON.parse(data);
                 $('#kField').val(json.k);
                 $('#mgField').val(json.mg);
                 $('#feField').val(json.fe);
                 $('#rinverdenteField').val(json.rinverdente);
                 $('#pField').val(json.p);
                 $('#nField').val(json.n);
                 $('#npkField').val(json.npk);
                 $('#idQ').val(id);
                 $('#dateQ').val(json.data);
                 $('#tridQ').val(trid);
                }
   			  })
   			});
			$(document).on('click','.deleteBtnF',function(event){
       			var table = $('#fertilizationTable').DataTable();
      			event.preventDefault();
                var id = $(this).data('id');
                if(confirm("Are you sure want to delete this quantities? ")){
                  $.ajax({
                    url:"Fertilization/delete_quantities.php",
                    data:{id:id},
                    type:"post",
                    success:function(data){
                      var json = JSON.parse(data);
                      status = json.status;
                      if(status=='success'){
                        //table.fnDeleteRow( table.$('#' + id)[0] );
                         $("#fertilizationTable tbody").find(id).remove();
                         //table.row($(this).closest("tr")) .remove();
                         //$("#"+id).closest('tr').remove();
                         table.draw();
                      }else{
                        alert('Failed');
                        return;
                      }
                    }
                  });
                }else{
              		return null;
            	}
          	})
        </script>
        <script type="text/javascript">
			$(document).ready(function() {
      			$('#waterValuesTable').DataTable({
        			"fnCreatedRow": function( nRow, aData, iDataIndex ) {
          				$(nRow).attr('id', aData[0]);},
                    "scrollX": true,
                    "pageLength": 5,
                    "lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
                    'serverSide':'true',
                    'processing':'true',
                    'paging':'true',
                    'order':[],
                    'ajax': {
                      'url':'waterValuesDirectory/fetch_data.php',
                      'type':'post',
                    },
                    "columnDefs": [{
                      'target':[5],
                      'orderable' :false,
                    }]
                });
    		});
    		$(document).on('submit','#addUser',function(e){
      			e.preventDefault();
                var key= $('#addKeyField').val();
                var no2= $('#addNo2Field').val();
                var no3= $('#addNo3Field').val();
                var gh= $('#addGHField').val();
                var kh= $('#addKHField').val();
                var po4= $('#addPo4Field').val();
                if (key == "Pia12"){
                    $.ajax({
                      url:"waterValuesDirectory/add_user.php",
                      type:"post",
                      data:{no2:no2, no3:no3, gh:gh, kh:kh, po4:po4},
                      success:function(data){
                        var json = JSON.parse(data);
                        var status = json.status;
                        if(status=='true'){
                          mytable =$('#waterValuesTable').DataTable();
                          mytable.draw();
                          $('#addWVModal').modal('hide');
                          var frm = document.getElementsByName('addUser')[0];
                          frm.reset();  // Reset all form data
                        }else{
                          alert('failed');
                        }
                      }
                    });
    			}else{
      				alert('Key wrong');
    			}
  			});
    		$(document).on('submit','#updateUser',function(e){
      			e.preventDefault();
                //var tr = $(this).closest('tr');
                var key= $('#keyField').val();
                var no2= $('#no2Field').val();
                var no3= $('#no3Field').val();
                var gh= $('#ghField').val();
                var kh= $('#khField').val();
                var po4= $('#po4Field').val();
                var trid= $('#trid').val();
                var id= $('#id').val();
                var date= $('#date').val();
                if (key == "Pia12"){
                	//if(no2 != '' && no3 != '' && gh != '' && kh != '' ) {
                       $.ajax({
                         url:"waterValuesDirectory/update_user.php",
                         type:"post",
                         data:{no2:no2, no3:no3, gh:gh, kh:kh, po4:po4, id:id},
                         success:function(data){
                           var json = JSON.parse(data);
                           var status = json.status;
                           if(status=='true'){
                            table =$('#waterValuesTable').DataTable();
                            // table.cell(parseInt(trid) - 1,0).data(id);
                            // table.cell(parseInt(trid) - 1,1).data(username);
                            // table.cell(parseInt(trid) - 1,2).data(email);
                            // table.cell(parseInt(trid) - 1,3).data(mobile);
                            // table.cell(parseInt(trid) - 1,4).data(city);
                            var button =   '<td><a href="javascript:void();" data-id="' +id + '" class="btn btn-info btn-sm editbtn">Edit</a>  <a href="#!"  data-id="' +id + '"  class="btn btn-danger btn-sm deleteBtn">Delete</a></td>';
                            var row = table.row("[id='"+trid+"']");
                            var dateClass = new Date(date)
                            var dateFormat = dateClass.getDate()+"/"+(dateClass.getMonth()+1)+"/"+dateClass.getFullYear()
                            row.row("[id='" + trid + "']").data([dateFormat, no2, no3, gh, kh, po4, button]);
                            $('#updateVWModal').modal('hide');
                           }else{
                            alert('failed');
                           }
                         }
                       });
                    //}else{
        			//   alert('Fill all the required fields');
       			    //}
      			}else{
      				alert('Key wrong');
    			}
    		});
    		$('#waterValuesTable').on('click','.editbtn ',function(event){
              var table = $('#waterValuesTable').DataTable();
              var trid = $(this).closest('tr').attr('id');
              // console.log(selectedRow);
              var id = $(this).data('id');
              $('#updateVWModal').modal('show');
              $.ajax({
                url:"waterValuesDirectory/get_single_data.php",
                data:{id:id},
                type:'post',
                success:function(data){
                 var json = JSON.parse(data);
                 $('#no2Field').val(json.no2);
                 $('#no3Field').val(json.no3);
                 $('#ghField').val(json.gh);
                 $('#khField').val(json.kh);
                 $('#po4Field').val(json.po4);
                 $('#id').val(id);
                 $('#date').val(json.data);
                 $('#trid').val(trid);
                }
   			  })
   			});
    		$(document).on('click','.deleteBtn',function(event){
       			var table = $('#waterValuesTable').DataTable();
      			event.preventDefault();
                var id = $(this).data('id');
                if(confirm("Are you sure want to delete this values?")){
                  $.ajax({
                    url:"waterValuesDirectory/delete_user.php",
                    data:{id:id},
                    type:"post",
                    success:function(data){
                      var json = JSON.parse(data);
                      status = json.status;
                      if(status=='success'){
                        //table.fnDeleteRow( table.$('#' + id)[0] );
                         $("#waterValuesTable tbody").find(id).remove();
                         //table.row($(this).closest("tr")) .remove();
                         //$("#"+id).closest('tr').remove();
                         table.draw();
                      }else{
                        alert('Failed');
                        return;
                      }
                    }
                  });
                }else{
              		return null;
            	}
          	})
        </script>
    	<!-- Scripts 
    	<script src="sito/assets/js/jquery.min.js"></script>-->
    <script src="assets/js/jquery.dropotron.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <!-- <script src="sito/assets/js/main.js"></script> -->
    <!-- oppure <script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script> -->
    <script src="assets\js\flickity.pkgd.min.js"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
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
            //    ['ppm', <?php //echo $tds;?>],
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
                yellowFrom: 600, yellowTo: 820,
                redFrom: 820, redTo: 1000,
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
        <!-- Modal -->
        <div class="modal fade" id="updateVWModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<h5 class="modal-title" id="exampleModalLabel">Update Values</h5>
        				<button7 type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button7>
      				</div>
  				    <div class="modal-body">
        				<form id="updateUser" >
          					<input type="hidden" name="id" id="id" value="">
                            <input type="hidden" name="date" id="date" value="">
          					<input type="hidden" name="trid" id="trid" value="">
                            <div class="mb-3 row">
            					<label for="keyField" class="col-md-3 form-label">Key</label>
            					<div class="col-md-9">
              						<input type="password" class="form-control" id="keyField" name="key" >
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="no2Field" class="col-md-3 form-label">Nitriti</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="no2Field" name="nitriti" >
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="no3Field" class="col-md-3 form-label">Nitrati</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="no3Field" name="nitrati">
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="ghField" class="col-md-3 form-label">GH</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="ghField" name="gh">
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="khField" class="col-md-3 form-label">KH</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="khField" name="kh">
           						</div>
          					</div>
                            <div class="mb-3 row">
            					<label for="po4Field" class="col-md-3 form-label">Fosfati</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="po4Field" name="fosfati">
           						</div>
          					</div>
          					<div class="text-center">
            					<button type="submit" class="btn btn-primary">Submit</button>
          					</div>
        				</form> 
      				</div>
      				<div class="modal-footer">
        				<button type="button"  class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      				</div>
    			</div>
  			</div>
		</div>
    	<!-- Add user Modal -->
    	<div class="modal fade" id="addWVModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<h5 class="modal-title" id="exampleModalLabel">Add Values</h5>
        				<button7 type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button7>
      				</div>
      				<div class="modal-body">
        				<form id="addUser" name="addUser" action="">
             				<div class="mb-3 row">
            					<label for="addKeyField" class="col-md-3 form-label">Key</label>
            					<div class="col-md-9">
              						<input type="password" class="form-control" id="addKeyField" name="key" >
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="addNo2Field" class="col-md-3 form-label">Nitriti</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="addNo2Field" name="no2" >
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="addNo3Field" class="col-md-3 form-label">Nitrati</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="addNo3Field" name="no3">
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="addGHField" class="col-md-3 form-label">GH</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="addGHField" name="gh">
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="addKHField" class="col-md-3 form-label">KH</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="addKHField" name="kh">
            					</div>
          					</div>
                            <div class="mb-3 row">
            					<label for="addPo4Field" class="col-md-3 form-label">Fosfati</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="addPo4Field" name="po4">
            					</div>
          					</div>
          					<div class="text-center">
            					<button type="submit" class="btn btn-primary">Submit</button>
          					</div>
        				</form> 
      				</div>
     				<div class="modal-footer">
        				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      				</div>
    			</div>
  			</div>
		</div>  
		<!-- Add Fertilization Modal -->
    	<div class="modal fade" id="addFertilizationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<h5 class="modal-title" id="exampleModalLabel">Add Quantities</h5>
        				<button7 type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button7>
      				</div>
      				<div class="modal-body">
        				<form id="addQuantities" name="addQuantities" action="">
             				<div class="mb-3 row">
            					<label for="addKeyQField" class="col-md-3 form-label">Key</label>
            					<div class="col-md-9">
              						<input type="password" class="form-control" id="addKeyQField" name="key" >
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="addKField" class="col-md-3 form-label">Potassio</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="addKField" name="potassio" >
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="addMgField" class="col-md-3 form-label">Magnesio</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="addMgField" name="magnesio">
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="addFeField" class="col-md-3 form-label">Ferro</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="addFeField" name="ferro">
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="addRinverdenteField" class="col-md-3 form-label">Rinverdente</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="addRinverdenteField" name="rinverdente">
            					</div>
          					</div>
                            <div class="mb-3 row">
            					<label for="addPField" class="col-md-3 form-label">Fosforo</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="addPField" name="fosforo">
            					</div>
          					</div>
                            <div class="mb-3 row">
            					<label for="addNField" class="col-md-3 form-label">Azoto</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="addNField" name="azoto">
            					</div>
          					</div>
							<div class="mb-3 row">
            					<label for="addNPKField" class="col-md-3 form-label">Stick NPK</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="addNPKField" name="npk">
            					</div>
          					</div>
          					<div class="text-center">
            					<button type="submit" class="btn btn-primary">Submit</button>
          					</div>
        				</form> 
      				</div>
     				<div class="modal-footer">
        				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      				</div>
    			</div>
  			</div>
		</div>
        <!-- Modal Fertilization -->
        <div class="modal fade" id="updateFertilizationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  			<div class="modal-dialog">
    			<div class="modal-content">
      				<div class="modal-header">
        				<h5 class="modal-title" id="exampleModalLabel">Update Quantities</h5>
        				<button7 type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button7>
      				</div>
  				    <div class="modal-body">
        				<form id="updateQuantities" >
          					<input type="hidden" name="idQ" id="idQ" value="">
                            <input type="hidden" name="dateQ" id="dateQ" value="">
          					<input type="hidden" name="tridQ" id="tridQ" value="">
                            <div class="mb-3 row">
            					<label for="keyQField" class="col-md-3 form-label">Key</label>
            					<div class="col-md-9">
              						<input type="password" class="form-control" id="keyQField" name="key" >
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="kField" class="col-md-3 form-label">Potassio</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="kField" name="potassio" >
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="mgField" class="col-md-3 form-label">Magnesio</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="mgField" name="magnesio">
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="feField" class="col-md-3 form-label">Ferro</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="feField" name="ferro">
            					</div>
          					</div>
          					<div class="mb-3 row">
            					<label for="rinverdenteField" class="col-md-3 form-label">Rinverdente</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="rinverdenteField" name="rinverdente">
           						</div>
          					</div>
                            <div class="mb-3 row">
            					<label for="pField" class="col-md-3 form-label">Fosforo</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="pField" name="fosforo">
           						</div>
          					</div>
                            <div class="mb-3 row">
            					<label for="nField" class="col-md-3 form-label">Azoto</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="nField" name="azoto">
           						</div>
          					</div>
							<div class="mb-3 row">
            					<label for="npkField" class="col-md-3 form-label">Stick NPK</label>
            					<div class="col-md-9">
              						<input type="number" step="any" class="form-control" id="npkField" name="npk">
           						</div>
          					</div>
          					<div class="text-center">
            					<button type="submit" class="btn btn-primary">Submit</button>
          					</div>
        				</form> 
      				</div>
      				<div class="modal-footer">
        				<button type="button"  class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      				</div>
    			</div>
  			</div>
		</div>
	</body>
</html>
<script type="text/javascript">
  //var table = $('#waterValuesTable').DataTable();
</script>