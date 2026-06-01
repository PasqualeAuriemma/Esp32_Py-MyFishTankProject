<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
<!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
     <title>PIA12 FISH TANK - AQUARIUM</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.theme.default.min.css">


    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"/>
    
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/salmon.png" />
    <style>
       .form-control:focus{border-color: #1010d1; color: #c6c6e6;  box-shadow: none; -webkit-box-shadow: none;} 
       .has-error .form-control:focus{box-shadow: none; -webkit-box-shadow: none;}
       table, th, td {
          text-align: center;
       }
    </style>
</head>
<script>
function showVolumes() {
  try {
    // Opera 8.0+, Firefox, Safari
    var xmlhttp = new XMLHttpRequest();
  }catch (e) {
    // Internet Explorer Browsers
    try {
      var xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    }catch (e) {
      try{
        var xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      }catch (e){
        // Something went wrong
        alert("Your browser broke!");
        return false;
      }
    }
  }
               
  xmlhttp.onreadystatechange = function() {
     if (this.readyState == 4 && this.status == 200) {
       document.getElementById("volumes").innerHTML = this.responseText;
     }
  };
  xmlhttp.open("GET","getFertilizationVolumes.php",true);
  xmlhttp.send();
}
</script>
<body class="sidebar-icon-only" onload="showVolumes()">
    <div class="container-scroller">
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <ul class="nav">
                <li class="nav-item nav-category">
                    <span class="nav-link">Navigation</span>
                </li>
                <li class="nav-item menu-items">
                    <a class="nav-link" href="index.php">
                        <span class="menu-icon">
                          <i class="mdi mdi-speedometer"></i>
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item menu-items">
                    <a class="nav-link" href="diary.php">
                        <span class="menu-icon">
                          <i class="mdi mdi-table-large"></i>
                        </span>
                        <span class="menu-title">Diary</span>
                    </a>
                </li>
                <li class="nav-item menu-items">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                        <span class="menu-icon">
							<i class="mdi mdi-playlist-play"></i>
						</span>
                        <span class="menu-title">Add Values</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="ui-basic">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"> <a class="nav-link" href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addWVModal">Water Values</a></li>
                            <li class="nav-item"> <a class="nav-link" href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addFertilizationModal">Fertilization</a></li>
                            <li class="nav-item"> <a class="nav-link" href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addConductivityModal">EC</a></li>
                            <li class="nav-item"> <a class="nav-link" href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addPHModal">PH</a></li>
                            <li class="nav-item"> <a class="nav-link" href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addTemperatureModal">Temperature</a></li>
                        </ul>
                    </div>
                </li>
                
            </ul>
        </nav>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_navbar.html -->
            <nav class="navbar p-0 fixed-top d-flex flex-row">
                <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="mdi mdi-menu"></span>
                    </button>
                    <ul class="navbar-nav navbar-nav-right">
                        <li class="nav-item dropdown">
                            <a class="nav-link" id="profileDropdown" href="#" data-bs-toggle="dropdown">
                                <div class="navbar-profile">
                                    <img class="img-xs rounded-circle" src="assets/images/faces/salmon.jpg" alt="">
                                    <p class="mb-0 d-none d-sm-block navbar-profile-name">AQUARIUM PIA12</p>
                                    <i class="mdi mdi-menu-down d-none d-sm-block"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="profileDropdown">
                                <h6 class="p-3 mb-0">Options</h6>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item preview-item" href="settings.php">
                                    <div class="preview-thumbnail">
                                        <div class="preview-icon bg-dark rounded-circle">
                                            <i class="mdi mdi-settings text-info"></i>
                                        </div>
                                    </div>
                                    <div class="preview-item-content">
                                        <p class="preview-subject mb-1">Settings</p>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                                <?php if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {?>
                                  <a class="dropdown-item preview-item" href="logout_db.php" data-id="" data-bs-toggle="modal" data-bs-target="#loginModal">
                                      <div class="preview-thumbnail">
                                          <div class="preview-icon bg-dark rounded-circle">
                                              <i class="mdi mdi-login text-success"></i>
                                          </div>
                                      </div>
                                      <div class="preview-item-content">
                                          <p class="preview-subject mb-1">Login In</p>
                                      </div>
                                  </a>
                                <?php }else{ ?>
                                  <a class="dropdown-item preview-item" id="logout">
                                      <div class="preview-thumbnail">
                                          <div class="preview-icon bg-dark rounded-circle">
                                              <i class="mdi mdi-logout text-danger"></i>
                                          </div>
                                      </div>
                                      <div class="preview-item-content">
                                          <p class="preview-subject mb-1">Log out</p>
                                      </div>
                                  </a>
                                <?php } ?>
                            </div>
                        </li>
                    </ul>
                    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                      <span class="mdi mdi-format-line-spacing"></span>
                    </button>
                </div>
            </nav>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">

                    <div class="row ">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <center><h3>Water Values</h3></center>
                                    <div class="table-responsive">
                                        <table id="waterValuesTable" class="table table-bordered" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Data</th>
                                                    <th>EC prima</th>
                                                    <th>EC dopo</th>
                                                    <th>PH</th>
                                                    <th>Nitriti</th>
                                                    <th>Nitrati</th>
                                                    <th>GH</th>
                                                    <th>KH</th>
                                                    <th>Fosfati</th>
                                                    <th>Options</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <?php if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {?>
                                                        
                                            <?php }else{ ?> 
                                              <tfoot>
                                                  <tr>
                                                      <th></th>
                                                      <th><input type="number" step="any" class="form-control" id="addNo2Field" name="no2"></th>
                                                      <th><input type="number" step="any" class="form-control" id="addNo3Field" name="no3"></th>
                                                      <th><input type="number" step="any" class="form-control" id="addGHField" name="gh"></th>
                                                      <th><input type="number" step="any" class="form-control" id="addKHField" name="kh"></th>
                                                      <th><input type="number" step="any" class="form-control" id="addPo4Field" name="po4"></th>
                                                      <th><a href="#!" data-id="'.$row['id'].'" class="btn btn-primary addBtn" >Add</a></th>      
                                                  </tr>
                                              </tfoot>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <center><h3>Fertilization Diary</h3></center>
                                    <div class="table-responsive">
                                        <table id="fertilizationTable" class="table table-bordered" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Data</th>
                                                    <th>Potassio ml</th>
                                                    <th>Magnesio ml</th>
                                                    <th>Ferro ml</th>
                                                    <th>Rinverdente ml</th>
                                                    <th>Fosforo ml</th>
                                                    <th>Azoto ml</th>
                                                    <th>NPK</th>
                                                    <th>Options</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <?php if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {?>
                                						  
                               				<?php }else{ ?>
                                              <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th><input type="number" step="any" class="form-control" id="addKField" name="potassio"></th>
                                                    <th><input type="number" step="any" class="form-control" id="addMgField" name="magnesio"></th>
                                                    <th><input type="number" step="any" class="form-control" id="addFeField" name="ferro"></th>
                                                    <th><input type="number" step="any" class="form-control" id="addRinverdenteField" name="rinverdente"></th>
                                                    <th><input type="number" step="any" class="form-control" id="addPField" name="fosforo"></th>
                                                    <th><input type="number" step="any" class="form-control" id="addNField" name="azoto"></th>
                                                    <th><input type="number" step="any" class="form-control" id="addNPKField" name="npk"></th>
                                                    <th><a href="#!" data-id="'.$row['id'].'" class="btn btn-primary addFertilizationBtn" >Add</a></th>
                                                </tr>
                                              </tfoot>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="volumes">
                    
                  </div>
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <footer class="footer">
                  <div class="content-wrapper">
                    <div class="row">
                        <div class="col-sm-4 grid-margin">
                        	<section>
                                <h3 class="icon solid fa-comment">Social</h3>
                                <center><p>
                                  <a href="https://github.com/PasqualeAuriemma/MyFishTankProject">Github</a><br />
                                  <a href="https://www.linkedin.com/in/pasquale-auriemma-780953b8/">LinkedIn</a><br />
                                  <a href="https://it.altervista.org/">Altervista</a>
                                </p></center>
                        	</section>
                        </div>
                        <div class="col-sm-4 grid-margin">
                        	<section>
                            	<h3 class="icon solid fa-envelope">Email</h3>
                                	<center>
                                    	<p>
                                            <a href="#">info@untitled.tld</a>
                                        </p>
                                    </center>
                        	</section>
                        </div>
                        <div class="col-sm-4 grid-margin">
                        	<div id="copyright">
                            	<span class="text-muted d-block text-center text-sm-left d-sm-inline-block"> <a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img alt="Licenza Creative Commons" style="border-width:0" src="https://i.creativecommons.org/l/by/4.0/88x31.png" /></a></span>
                            	<span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Quest'opera è distribuita con Licenza <a rel="license" href="http://creativecommons.org/licenses/by/4.0/">Creative Commons Attribuzione 4.0 Internazionale</a>.</span>
                      		</div>
                        </div>   
                    </div>                     
                  </div>  
                </footer>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    
    
    
    <script>
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("volumes").innerHTML = this.responseText;
      }
    };
    xmlhttp.open("GET","getFertilizationVolumes.php,true);
    xmlhttp.send();
  

</script>
    
    
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
     <script src="assets/vendors/chart.js/Chart.min.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="assets/vendors/owl-carousel-2/owl.carousel.min.js"></script>
    <script src="assets/js/jquery.cookie.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <!--<script src="assets/js/data-picker.js"></script>-->
    <!--<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.3.1.js"></script> -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>

    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="assets/js/dashboard.js"></script>
    <script src="dayHistoryValuesAdding.js"></script>
    <script src="fertilizationTable.js"></script>
    <script src="waterValuesTable.js"></script>
    <script type="text/javascript">
    	$(document).ready(function () {
            $("#login").on('click', function() {
            	var email = $("#email").val();
                var password = $("#password").val();
                $.ajax(	
                {
                    url: 'login_db.php',
                    method: 'POST',
                    data: {
                        login: 1, 
                        emailPHP: email,
                        passwordPHP: password
                          },
                    success: function(response) {
                    	console.log(response);
                         $('#loginModal').modal('hide');
                         location.reload(true);
                    },
                    dataType: 'text'
                }
              );    
            });
            $("#logout").on('click', function() {
                $.ajax(	
                {
                    url: 'logout_db.php',
                    method: 'POST',
                    data: {
                        login: 0, 
                          },
                    success: function(response) {
                    	console.log(response);
                        location.reload(true);
                    },
                    dataType: 'text'
                }
              );    
            });
        });
    </script>
    <!-- End custom js for this page -->    
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
              						<input type="number" step="any" class="form-control" id="nField" name="n">
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
       <!-- Add Temperature Modal -->
       <div class="modal fade" id="addTemperatureModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Temperature</h5>
                    <button7 type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button7>
                </div>
                <div class="modal-body">
                    <form id="addTemperature" name="addConductivity" action="">
                        
                        <div class="mb-3 row">
                            <label for="addTField" class="col-md-3 form-label">Temperature</label>
                            <div class="col-md-9">
                                <input type="number" step="any" class="form-control" id="addTField" name="temperature">
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
    <!-- Add Conductivity Modal -->
    <div class="modal fade" id="addConductivityModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Conductivity value</h5>
                    <button7 type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button7>
                </div>
                <div class="modal-body">
                    <form id="addConductivity" name="addConductivity" action="">
                        
                        <div class="mb-3 row">
                            <label for="addECField" class="col-md-3 form-label">Conductivity</label>
                            <div class="col-md-9">
                                <input type="number" step="any" class="form-control" id="addECField" name="ec">
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
    <!-- Add PH Modal -->
    <div class="modal fade" id="addPHModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add PH value</h5>
                    <button7 type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button7>
                </div>
                <div class="modal-body">
                    <form id="addPH" name="addPH" action="">
                        
                        <div class="mb-3 row">
                            <label for="addPHField" class="col-md-3 form-label">PH</label>
                            <div class="col-md-9">
                                <input type="number" step="any" class="form-control" id="addPHField" name="ph">
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
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
          	<div class="modal-header">
                    <h3 class="card-title text-left mb-3">Login</h3>
                    <button7 type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button7>
            </div>
            <div class="modal-body">         
               <form action="diary.php" method="post" id="loginForm">
                 <div class="form-group">
                    <input type="text" class="form-control p_input" placeholder="Username or email" id="email" required>
                 </div>
                 <div class="form-group">
                    <input type="password" class="form-control p_input" placeholder="Password" id="password" required>
                   </div>
                 <div class="form-group d-flex align-items-center justify-content-between">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input type="submit" class="form-check-input"> Remember me </label>
                    </div>
                    <a href="#" class="forgot-pass">Forgot password</a>
                  </div>
                  <div class="text-center">
                    <input type="button" class="btn btn-primary btn-block enter-btn" id="login" value="Log in">
                  </div>
                  <br />
                  <div class="d-flex">
                    <button class="btn btn-facebook me-2 col">
                      <i class="mdi mdi-facebook"></i> Facebook </button>
                    <button class="btn btn-google col">
                      <i class="mdi mdi-google-plus"></i> Google plus </button>
                </div>
                <div class="modal-footer">
                	  <p class="sign-up">Don't have an Account?<a href="#"> Sign Up</a></p> 
                </div>
              </form>
            </div>
        </div>
         </div>
    </div>   
        
</body>

</html>