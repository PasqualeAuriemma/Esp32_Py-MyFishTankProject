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
    <?php
        include("assets/js/php/connection.php");
        if ($con->connect_error) {
          die("Connection failed: " . $con->connect_error);
        }

        $q = 0;
 
        $sql_max = "SELECT fertilizzante AS name, qnt, data_inizio AS date FROM my_myfishtank.fertilization_volumes";

        $query = mysqli_query($con, $sql_max);

        $sub_array = array();
        $sub_array_quantities = array();
        $sub_array_data = array();
       
        while($row = mysqli_fetch_assoc($query)){
            $sub_array += [$q => number_format($row['qnt'], 2, '.', '')];
            $sub_array_data += [$q => $row['date']];
            $q = $q + 1;
        }

        $con->close();
?>
</head>

<body class="sidebar-icon-only" >
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
            </ul>
        </nav>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_navbar.html -->
            <nav class="navbar p-0 fixed-top d-flex flex-row">
                <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
                    
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
                                <a class="dropdown-item preview-item">
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
                                  <a class="dropdown-item preview-item" href="login_db.php" data-iddata-iddata-iddata-id="" data-bs-toggle="modal" data-bs-target="#loginModal">
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
                                  <a class="dropdown-item preview-item" href="logout_db.php" id="logout">
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

                    <div class="row">
                        <div class="col-md-5 col-xl-4 grid-margin stretch-card">
                            <div class="card">
								<a class="nav-link" href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addConductivityModal">EC</a>
                                <a class="nav-link" href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addPHModal">PH</a>
                                <a class="nav-link" href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addTemperatureModal">Temperature</a>
                           </div>
                        </div>
                        <div class="col-md-5 col-xl-4 grid-margin stretch-card">
                            <div class="card">
								<div class="card-body">
                                    <a class="nav-link" href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addWVModal">Water Values</a>
                                    <a class="nav-link" href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addFertilizationModal">Fertilization</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-xl-4 grid-margin stretch-card">
                            <div class="card">
                           
                           </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                  <div id="volume">
                                   <div class="table-responsive">
                                     <table  class="table table-bordered" style="width:100%;">
                                     <thead>
                                       <tr>
                                            <th>Item</th>
                                            <th>Date</th>
                                            <th>Quantity or days</th>
                                            <th>Option</th>
                                       </tr>
                                     </thead>
                                     <tbody>
                                       <tr>  
                                          <form id="addSettingP" name="addSettingP" action="">
                                            <td><h5 style="color: #f8f9fa;">Potassium</h5></td>
                                            <td><input type="text" name="datepicker_SettingsP" id="datepicker_SettingsP" placeholder="<?php echo $sub_array_data[0];?>" /></td>         
                                            <td><input type="number" step="any" class="form-control" value="<?php echo $sub_array[0];?>" id="addFieldSettingsP" name="Potassio"></td>
                                            <td><div class="text-center"><button type="submit" class="btn btn-primary">Submit</button></div></td>
                                          </form>   
                                       </tr>                                             
                                       <tr>
                                          <form id="addSettingsM" name="addSettingsM" action="">
                                            <td><h5 style="color: #f8f9fa;">Magnesium</h5></td>
                                            <td><input type="text" name="datepicker_SettingsM" id="datepicker_SettingsM" placeholder="<?php echo $sub_array_data[1];?>"/></td>
                                            <td><input type="number" step="any" class="form-control" value="<?php echo $sub_array[1];?>" id="addFieldSettingsM" name="Magnesium"></td>
                                            <td><div class="text-center"><button type="submit" class="btn btn-primary">Submit</button></div></td>
                                          </form>
                                       </tr> 
                                       <tr>
                                         <form id="addSettingsI" name="addSettingsI" action="">
                                            <td><h5 style="color: #f8f9fa;">Iron</h5></td> 
                                            <td><input type="text" name="datepicker_SettingsI" id="datepicker_SettingsI" placeholder="<?php echo $sub_array_data[2];?>" /></td>
                                            <td><input type="number" step="any" class="form-control" value="<?php echo $sub_array[2];?>" id="addFieldSettingsI" name="Iron"></td>   
                                            <td><div class="text-center"><button type="submit" class="btn btn-primary">Submit</button></div></td>
                                         </form>
                                       </tr> 
                                       <tr>
                                         <form id="addSettingsRin" name="addSettingsRin" action="">
                                            <td><h5 style="color: #f8f9fa;">Rinverdente</h5></td> 
                                            <td><input type="text" name="datepicker_SettingsRin" id="datepicker_SettingsRin" placeholder="<?php echo $sub_array_data[3];?>" /></td>
                                            <td><input type="number" step="any" class="form-control" value="<?php echo $sub_array[3];?>" id="addFieldSettingsRin" name="Rinverdente"></td>
                                            <td><div class="text-center"><button type="submit" class="btn btn-primary">Submit</button></div></td>
                                         </form> 
                                       </tr> 
                                       <tr>
                                         <form id="addSettingsPho" name="addSettingsPho" action="">
                                            <td><h5 style="color: #f8f9fa;">Phosphorus</h5></td>
                                            <td><input type="text" name="datepicker_SettingsPho" id="datepicker_SettingsPho" placeholder="<?php echo $sub_array_data[4];?>" /></td>
                                            <td><input type="number" step="any" class="form-control" size="16" value="<?php echo $sub_array[4];?>" id="addFieldSettingsPho" name="Phosphorus"></td>
                                            <td><div class="text-center"><button type="submit" class="btn btn-primary">Submit</button></div></td>
                                         </form>  
                                       </tr> 
                                       <tr>
                                         <form id="addSettingsN" name="addSettingsN" action="">
                                            <td><h5 style="color: #f8f9fa;">Nitrogen</h5></td>
                                            <td><input type="text" name="datepicker_SettingsN" id="datepicker_SettingsN" placeholder="<?php echo $sub_array_data[5];?>" /></td>
                                            <td><input type="number" step="any" class="form-control" value="<?php echo $sub_array[5];?>" id="addFieldSettingsN" name="Nitrogen"></td>
                                            <td><div class="text-center"><button type="submit" class="btn btn-primary">Submit</button></div></td>
                                         </form> 
                                       </tr>      
                                       <tr>
                                         <form id="addSettingsStick" name="addSettingsStick" action="">
                                            <td><h5 style="color: #f8f9fa;">Stick NPK</h5></td>
                                            <td><input type="text" name="datepicker_SettingsS" id="datepicker_SettingsS" placeholder="<?php echo $sub_array_data[6];?>" /></td>
                                            <td><input type="number" step="any" class="form-control" value="<?php echo $sub_array[6];?>" id="addFieldSettingsS" name="Stick"></td>
                                            <td><div class="text-center"><button type="submit" class="btn btn-primary">Submit</button></div></td>
                                         </form>
                                       </tr>
                                       <tr> 
                                          <form id="addSettingsCo2" name="addSettingsCo2" action="">
                                            <td><h5 style="color: #f8f9fa;">Co2</h5></td>
                                            <td><input type="text" name="datepicker_SettingsCo2" id="datepicker_SettingsCo2" placeholder="<?php echo $sub_array_data[7];?>" /></td>
                                            <td><input type="number" step="any" class="form-control" value="<?php echo $sub_array[7];?>" id="addFieldSettingsCo2" name="Co2"></td>
                                            <td><div class="text-center"><button type="submit" class="btn btn-primary">Submit</button></div></td>
                                          </form>
                                       </tr>
                                     </tbody>
                                    </table>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                  <H2>PH</H2>
                                  <div class="table-responsive">
                                    <table id="phTableSetting" class="table table-bordered" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>PH</th>
                                                <th>Date Send</th>
                                                <th>Date Arrive</th>
                                                <th>Options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                  <H2>Electrical Conductivity</H2>
                                  <div class="table-responsive">
                                    <table id="ecTableSetting" class="table table-bordered" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>EC</th>
                                                <th>Date Send</th>
                                                <th>Date Arrive</th>
                                                <th>Options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                  <H2>Total Dissolved Solids</H2>
                                  <div class="table-responsive">
                                    <table id="tdsTableSetting" class="table table-bordered" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>TDS</th>
                                                <th>Date Send</th>
                                                <th>Date Arrive</th>
                                                <th>Options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                  <H2>Temperature</H2>
                                  <div class="table-responsive">
                                    <table id="tempTableSetting" class="table table-bordered" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Temperature</th>
                                                <th>Date Send</th>
                                                <th>Date Arrive</th>
                                                <th>Options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                            </div>
                        </div>
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
	<script src="settings_handleVolumesDB.js"></script>
    <script src="phTableSetting.js"></script>
    <script src="ecTableSetting.js"></script>
    <script src="tdsTableSetting.js"></script>
    <script src="tempTableSetting.js"></script>
	
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
    
    <script type="text/javascript">
  

    </script>
    <!-- End custom js for this page --> 
	
    <!-- Modal -->
    <!-- Add water value Modal -->
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
                <label for="addecPField" class="col-md-3 form-label">EC Before</label>
                <div class="col-md-9">
                  <input type="number" step="any" class="form-control" id="addecPField" name="ecP" >
                </div>
              </div>
              <div class="mb-3 row">
                <label for="addecAField" class="col-md-3 form-label">EC After</label>
                <div class="col-md-9">
                  <input type="number" step="any" class="form-control" id="addecAField" name="ecA" >
                </div>
              </div>
              <div class="mb-3 row">
                <label for="addphField" class="col-md-3 form-label">PH</label>
                <div class="col-md-9">
                  <input type="number" step="any" class="form-control" id="addphField" name="ph" >
                </div>
              </div>
              <div class="mb-3 row">
                <label for="addNo2Field" class="col-md-3 form-label">Nitrites</label>
                <div class="col-md-9">
                  <input type="number" step="any" class="form-control" id="addNo2Field" name="no2" >
                </div>
              </div>
              <div class="mb-3 row">
                <label for="addNo3Field" class="col-md-3 form-label">Nitrates</label>
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
                <label for="addPo4Field" class="col-md-3 form-label">Phosphates</label>
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
                <label for="addKField" class="col-md-3 form-label">Potassium</label>
                <div class="col-md-9">
                  <input type="number" step="any" class="form-control" id="addKField" name="potassio" >
                </div>
              </div>
              <div class="mb-3 row">
                <label for="addMgField" class="col-md-3 form-label">Magnesium</label>
                <div class="col-md-9">
                  <input type="number" step="any" class="form-control" id="addMgField" name="magnesio">
                </div>
              </div>
              <div class="mb-3 row">
                <label for="addFeField" class="col-md-3 form-label">Iron</label>
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
                <label for="addPField" class="col-md-3 form-label">Phosphorus</label>
                <div class="col-md-9">
                  <input type="number" step="any" class="form-control" id="addPField" name="fosforo">
                </div>
              </div>
              <div class="mb-3 row">
                <label for="addNField" class="col-md-3 form-label">Nitrogen</label>
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
    <!-- Add Temperature Modal -->
    <div class="modal fade" id="addTemperatureModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Temperature</h5>
                    <button7 type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button7>
                </div>
                <div class="modal-body">
                    <form id="addTemperatureForm" name="addTemperatureForm" action="">
                        <div class="mb-3 row">
                            <label for="addTField" class="col-md-3 form-label">Temperature</label>
                            <div class="col-md-9">
                                <input  type="number" step="any" class="form-control" id="addTFieldP" name="temperature">
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
                    <form id="addConductivityForm" name="addConductivityForm" action="">
                        <div class="mb-3 row">
                            <label for="addECFieldP" class="col-md-3 form-label">Conductivity</label>
                            <div class="col-md-9">
                                <input type="number" step="any" class="form-control" id="addECFieldP" name="ec">
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
                    <form id="addPHForm" name="addPHForm" action="">
                        <div class="mb-3 row">
                            <label for="addPHFieldP" class="col-md-3 form-label">PH</label>
                            <div class="col-md-9">
                                <input type="number" step="any" class="form-control" id="addPHFieldP" name="ph">
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