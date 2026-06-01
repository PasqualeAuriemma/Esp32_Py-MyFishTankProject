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
        <link href="css/bootstrap5.0.1.min.css" rel="stylesheet"  crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/datatables-1.10.25.min.css"/>
        <style type="text/css">
        div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }
    .btnAdd {
      text-align: center;
      width: auto;
      margin-top: 0px;
      margin-bottom: 0px;
    }
  </style>
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
      <section id="highlights" class="wrapper style1">
        <div class="title">Temperature</div>
        <div class="container">
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
      </section>
      <!-- Main -->
      <section id="main" class="wrapper style5">
        <div class="title">EC &amp TDS</div>
        <div class="container">
        <center>
          <p class="style4"> Recorded on <?php echo gmdate("l jS \of F Y", $sendEC). " at ".gmdate("H:i:s", $sendEC);?></p>
        </center>
        <div class="row aln-center">
			<div class="col-4 col-12-medium">
			  <section class="highlight">
			    <div class="container_chart">
                  <div class="row_chart">
                    <div id="chart_ec"></div>
                  </div>
                </div>			
		      </section>
			</div>
			<div class="col-4 col-12-medium">
			  <section class="highlight">
  
                <div class="container_chart">
                  <div class="row_chart">
                    <div id="chart_tds"></div>
                  </div>
                </div>
		  	  </section>
            </div>
          </div>
          <center>
            <ul class="actions" >
                <li><a href="tds_ec.php" class="button style3">More Details</a></li>
            </ul>
          </center>     
        </div>
      </section> 
      <!-- Main -->
      <?php include('connection.php');?>
      <section id="main" class="wrapper style4">
        <div class="title">PH</div>
        <div class="container">         
                        <a href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addUserModal"   class="btn btn-success btn-sm" >Add User</a>
                            <table id="waterValues" class="table" style="width:100%">
                                <thead>
                                  <th>Data</th>
                                  <th>Nitriti</th>
                                  <th>Nitrati</th>
                                  <th>GH</th>
                                  <th>KH</th>
                                  <th>Options</th>
								</thead>
								<tbody>
								</tbody>
							</table>
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

    <!-- Scripts -->
    <script src="js/jquery-3.6.0.min.js"  crossorigin="anonymous"></script>
    	<!-- Option 1: Bootstrap Bundle with Popper -->

	<script src="js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
	<script type="text/javascript" src="js/dt-1.10.25datatables.min.js"></script>
    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
    -->
    <script type="text/javascript">
    $(document).ready(function() {
      $('#waterValues').DataTable({
        "fnCreatedRow": function( nRow, aData, iDataIndex ) {
          $(nRow).attr('id', aData[0]);
        },
        "scrollX": true,
        "pageLength": 5,
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        'serverSide':'true',
        'processing':'true',
        'paging':'true',
        'order':[],
        'ajax': {
          'url':'fetch_data.php',
          'type':'post',
        },
        "columnDefs": [{
          'target':[5],
          'orderable' :false,
        }]
      });
    } );
    $(document).on('submit','#addUser',function(e){
      e.preventDefault();
      var key= $('#addKeyField').val();
      var no2= $('#addNo2Field').val();
      var no3= $('#addNo3Field').val();
      var gh= $('#addGHField').val();
      var kh= $('#addKHField').val();
      if (key == "Pia12"){
      if(no2 != '' && no3 != '' && gh != '' && kh != '')
      {
       $.ajax({
         url:"add_user.php",
         type:"post",
         data:{no2:no2, no3:no3, gh:gh, kh:kh},
         success:function(data)
         {
           var json = JSON.parse(data);
           var status = json.status;
           if(status=='true')
           {
            mytable =$('#waterValues').DataTable();
            mytable.draw();
            $('#addUserModal').modal('hide');
          }
          else
          {
            alert('failed');
          }
        }
      });
     }
     else {
      alert('Fill all the required fields');
    }
    } else {
      alert('Key wrong');
    }
  });
    $(document).on('submit','#updateUser',function(e){
      e.preventDefault();
       //var tr = $(this).closest('tr');
       var city= $('#cityField').val();
       var username= $('#nameField').val();
       var mobile= $('#mobileField').val();
       var email= $('#emailField').val();
       var trid= $('#trid').val();
       var id= $('#id').val();
       if(city != '' && username != '' && mobile != '' && email != '' )
       {
         $.ajax({
           url:"update_user.php",
           type:"post",
           data:{city:city,username:username,mobile:mobile,email:email,id:id},
           success:function(data)
           {
             var json = JSON.parse(data);
             var status = json.status;
             if(status=='true')
             {
              table =$('#waterValues').DataTable();
              // table.cell(parseInt(trid) - 1,0).data(id);
              // table.cell(parseInt(trid) - 1,1).data(username);
              // table.cell(parseInt(trid) - 1,2).data(email);
              // table.cell(parseInt(trid) - 1,3).data(mobile);
              // table.cell(parseInt(trid) - 1,4).data(city);
              var button =   '<td><a href="javascript:void();" data-id="' +id + '" class="btn btn-info btn-sm editbtn">Edit</a>  <a href="#!"  data-id="' +id + '"  class="btn btn-danger btn-sm deleteBtn">Delete</a></td>';
              var row = table.row("[id='"+trid+"']");
              row.row("[id='" + trid + "']").data([id,username,email,mobile,city,button]);
              $('#updateWVModal').modal('hide');
            }
            else
            {
              alert('failed');
            }
          }
        });
       }
       else {
        alert('Fill all the required fields');
      }
    });
    $('#waterValues').on('click','.editbtn ',function(event){
      var table = $('#waterValues').DataTable();
      var trid = $(this).closest('tr').attr('id');
     // console.log(selectedRow);
     var id = $(this).data('id');
     $('#updateWVModal').modal('show');

     $.ajax({
      url:"get_single_data.php",
      data:{id:id},
      type:'post',
      success:function(data)
      {
       var json = JSON.parse(data);
       $('#nameField').val(json.username);
       $('#emailField').val(json.email);
       $('#mobileField').val(json.mobile);
       $('#cityField').val(json.city);
       $('#id').val(id);
       $('#trid').val(trid);
     }
   })
   });

    $(document).on('click','.deleteBtn',function(event){
       var table = $('#waterValues').DataTable();
      event.preventDefault();
      var id = $(this).data('id');
      if(confirm("Are you sure want to delete this User ? "))
      {
      $.ajax({
        url:"delete_user.php",
        data:{id:id},
        type:"post",
        success:function(data)
        {
          var json = JSON.parse(data);
          status = json.status;
          if(status=='success')
          {
            //table.fnDeleteRow( table.$('#' + id)[0] );
             //$("#waterValues tbody").find(id).remove();
             //table.row($(this).closest("tr")) .remove();
             $("#"+id).closest('tr').remove();
          }
          else
          {
            alert('Failed');
            return;
          }
        }
      });
      }
      else
      {
        return null;
      }



    })
 </script>
    
    
    
    <?php
       $con->close();
    ?>
    <!-- <script src="sito/assets/js/jquery.min.js"></script> --> 
    <script src="sito/assets/js/jquery.dropotron.min.js"></script>
    <script src="sito/assets/js/browser.min.js"></script>
    <script src="sito/assets/js/breakpoints.min.js"></script>
    <script src="sito/assets/js/util.js"></script>
    <!-- <script src="sito/assets/js/main.js"></script> -->
    <!-- oppure <script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script> -->
    <script src="sito/assets\js\flickity.pkgd.min.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
	    <!-- Optional JavaScript; choose one of the two! -->

    
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart', 'gauge']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var dataTemp = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Gradi °', <?php echo $temperature;?>],
        ]);
        
        var dataTDS = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['ppm', <?php echo $tds;?>],
        ]);

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

         var optionsTDS = {
            yellowFrom: 500, yellowTo: 550,
            redFrom: 550, redTo: 650,
            minorTicks: 10,
            max: 650,
            height: y,
            width: x
        }; 
        
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
          var chartS = new google.visualization.Gauge(document.getElementById('chart_tds'));
          var chartP = new google.visualization.Gauge(document.getElementById('chart_ph'));
          chartT.clearChart();
          chartT.draw(dataTemp, options);
          chartE.clearChart();
          chartE.draw(dataEC, optionsEC);
          chartS.clearChart();
          chartS.draw(dataTDS, optionsTDS);
          chartP.clearChart();
          chartP.draw(dataPH, optionsPH);
        }
        window.onload = resize();
        window.onresize = resize;
      }
    </script>  

    
    <!-- Modal -->
    <div class="modal fade" id="updateWVModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Update User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateUser" >
          <input type="hidden" name="id" id="id" value="">
          <input type="hidden" name="trid" id="trid" value="">
          <div class="mb-3 row">
            <label for="nameField" class="col-md-3 form-label">Name</label>
            <div class="col-md-9">
              <input type="text" class="form-control" id="nameField" name="name" >
            </div>
          </div>
          <div class="mb-3 row">
            <label for="emailField" class="col-md-3 form-label">Email</label>
            <div class="col-md-9">
              <input type="email" class="form-control" id="emailField" name="email">
            </div>
          </div>
          <div class="mb-3 row">
            <label for="mobileField" class="col-md-3 form-label">Mobile</label>
            <div class="col-md-9">
              <input type="text" class="form-control" id="mobileField" name="mobile">
            </div>
          </div>
          <div class="mb-3 row">
            <label for="cityField" class="col-md-3 form-label">City</label>
            <div class="col-md-9">
              <input type="text" class="form-control" id="cityField" name="City">
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
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Values</h5>
        <button7 type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button7>
      </div>
      <div class="modal-body">
        <form id="addUser" action="">
             <div class="mb-3 row">
            <label for="addKeyField" class="col-md-3 form-label">Key</label>
            <div class="col-md-9">
              <input type="text" class="form-control" id="addKeyField" name="key" >
            </div>
          </div>
          <div class="mb-3 row">
            <label for="addNo2Field" class="col-md-3 form-label">Nitriti</label>
            <div class="col-md-9">
              <input type="number" class="form-control" id="addNo2Field" name="no2" >
            </div>
          </div>
          <div class="mb-3 row">
            <label for="addNo3Field" class="col-md-3 form-label">Nitrati</label>
            <div class="col-md-9">
              <input type="number" class="form-control" id="addNo3Field" name="no3">
            </div>
          </div>
          <div class="mb-3 row">
            <label for="addGHField" class="col-md-3 form-label">GH</label>
            <div class="col-md-9">
              <input type="number" class="form-control" id="addGHField" name="gh">
            </div>
          </div>
          <div class="mb-3 row">
            <label for="addKHField" class="col-md-3 form-label">KH</label>
            <div class="col-md-9">
              <input type="number" class="form-control" id="addKHField" name="kh">
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
  </body>
</html>
<script type="text/javascript">
  //var table = $('#waterValues').DataTable();
</script>