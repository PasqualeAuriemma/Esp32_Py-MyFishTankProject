<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>PIA12 FISH TANK - AQUARIUM MANAGMENT</title>
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
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/salmon.png" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"/>
    
    <style>
       .form-control:focus{border-color: #1010d1; color: #c6c6e6;  box-shadow: none; -webkit-box-shadow: none;} 
       .has-error .form-control:focus{box-shadow: none; -webkit-box-shadow: none;}
       table, th, td {
          text-align: center;
       }
       .ui-widget {
            font-family: Verdana,Arial,sans-serif;
            font-size: .8em;
       }

       .ui-widget-content {
           background: #000000;
           border: 1px solid #000000;
           color: #222222;
       }

       .ui-dialog {
           left: 0;
           outline: 0 none;
           padding: 0 !important;
           position: absolute;
           width: 100%,
           top: 0;
           overflow:visible;
       }

       #success {
           padding: 0;
           margin: 0; 
       }

       .ui-dialog .ui-dialog-content {
           background: none repeat scroll 0 0 transparent;
           border: 0 none;
           overflow: auto;
           position: relative;
           padding: 0 !important;
           background: #000000;
       }

       .ui-widget-header {
           background: #434a54;
           border: 0;
           color: #fff;
           font-weight: normal;
       }
       
       .ui-dialog .ui-dialog-titlebar-close {
           right:0;
       }
       
       .ui-dialog .ui-dialog-titlebar {
           padding: 0.1em .5em;
           position: relative;
           font-size: 1em;
       }
       
       .ui-dialog .ui-dialog-buttonpane { border-width: 0 !important; }
       .ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset {
	      float: right;
       }
    </style>
    
    <?php
          include("php/connection.php");
          include("php/queryAndFunction.php");

          $dataNow1 = date('Y-m-d');  
          $sqlT = getDailyTemperature($dataNow1);
          $sqlEC = getDailyEC($dataNow1);
          $sqlPH =  getDailyPH($dataNow1);
             
          $resultT = $con->query($sqlT);
          $resultEC = $con->query($sqlEC);
          $resultPH = $con->query($sqlPH);
          
          $t_array = array();
          if ($resultT->num_rows > 0) {
            // output data of each row
            while($rowT = $resultT->fetch_assoc()) {
              $t_array[] = $rowT["temperature"];
              $sendT = $rowT["send_t"];
            }
          } else {
            $t_array[] = 0;
            $sendT = "no data";
          }
          $temperature = number_format(calculate_average($t_array), 2);
          
          $ec_array = array();
          if ($resultEC->num_rows > 0) {
            // output data of each row
            while($rowEC = $resultEC->fetch_assoc()) {
              $ec_array[] = $rowEC["ec"];
              $sendEC = $rowEC["send_e"];
            }
          } else {
            $ec_array[] = 0;
            $sendEC = "no data";
          }
          $ec = number_format( calculate_median($ec_array), 2);
          
          $ph_array = array();
          if ($resultPH->num_rows > 0) {
            // output data of each row
            while($rowPH = $resultPH->fetch_assoc()) {
              $ph_array[] = $rowPH["ph"];
              $sendPH = $rowPH["send_p"];
            }
          } else {
            $ph_array[] = 0;
            $sendPH = "no data";
          }
          $ph = number_format(calculate_median($ph_array), 2);
          
          $con->close();
    ?>
    
</head>

<body class="sidebar-icon-only">
    <div class="container-scroller">        
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <?php if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {?>
            <?php }else{ ?>
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
                      <span class="menu-icon"><i class="mdi mdi-laptop"></i></span>
                      <span class="menu-title">Basic UI Elements</span>
                      <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="ui-basic">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a></li>
                            <li class="nav-item"> <a class="nav-link" href="pages/ui-features/dropdowns.html">Dropdowns</a></li>
                            <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item menu-items">
                    <a class="nav-link" href="pages/forms/basic_elements.html">
                        <span class="menu-icon"><i class="mdi mdi-playlist-play"></i></span>
                        <span class="menu-title">Form Elements</span>
                    </a>
                </li>
                <li class="nav-item menu-items">
                    <a class="nav-link" href="pages/tables/basic-table.html">
                        <span class="menu-icon">
                          <i class="mdi mdi-table-large"></i>
                        </span>
                        <span class="menu-title">Tables</span>
                    </a>
                </li>
                <li class="nav-item menu-items">
                    <a class="nav-link" href="pages/charts/chartjs.html">
                        <span class="menu-icon"><i class="mdi mdi-chart-bar"></i></span>
                        <span class="menu-title">Charts</span>
                    </a>
                </li>
                <li class="nav-item menu-items">
                    <a class="nav-link" href="pages/icons/mdi.html">
                        <span class="menu-icon"><i class="mdi mdi-contacts"></i></span>
                        <span class="menu-title">Icons</span>
                    </a>
                </li>
                <li class="nav-item menu-items">
                    <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                        <span class="menu-icon"><i class="mdi mdi-security"></i></span>
                        <span class="menu-title">User Pages</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="auth">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"> <a class="nav-link" href="pages/samples/blank-page.html"> Blank Page </a></li>
                            <li class="nav-item"> <a class="nav-link" href="pages/samples/error-404.html"> 404 </a></li>
                            <li class="nav-item"> <a class="nav-link" href="pages/samples/error-500.html"> 500 </a></li>
                            <li class="nav-item"> <a class="nav-link" href="pages/samples/login.html"> Login </a></li>
                            <li class="nav-item"> <a class="nav-link" href="pages/samples/register.html"> Register </a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item menu-items">
                    <a class="nav-link" href="http://www.bootstrapdash.com/demo/corona-free/jquery/documentation/documentation.html">
                        <span class="menu-icon"><i class="mdi mdi-file-document-box"></i></span>
                        <span class="menu-title">Documentation</span>
                    </a>
                </li>
            </ul>
            <?php } ?>
        </nav>
        
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_navbar.html -->
            <nav class="navbar p-0 fixed-top d-flex flex-row">
                <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
                     <?php if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {?>
                     <?php }else{ ?>
                      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                      <span class="mdi mdi-menu"></span>
                      </button>
                    <?php } ?>
                    <ul class="navbar-nav navbar-nav-right">
                        <li class="nav-item dropdown">
                            <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
                                <div class="navbar-profile">
                                    <button class="btn btn-outline-primary btn-rounded btn-icon" id="opener" aria-labelledby="profileDropdown">
                                        <i class= "mdi mdi-water text-primary"></i><!-- <i class="mdi mdi-pulse"></i>
                                        <i class= "mdi mdi-stethoscope"></i>-->
                                    </button>   
                                </div>
                            </a>
                        </li>
                        <li class="dropdown nav-item">
                            <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
                                <div class="navbar-profile">
                                    <button class="btn btn-outline-success btn-rounded btn-icon" id="openFertilization" aria-labelledby="profileDropdown">
                                        <i class="mdi mdi-eyedropper text-success" ></i><!--<i class="mdi mdi-book-open-variant"></i>
                                         <i class="mdi mdi-cup-water"></i>-->
                                    </button>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
                                <div class="navbar-profile">
                                    <button class="btn btn-outline-danger btn-rounded btn-icon" id="openVolumes" aria-labelledby="profileDropdown">
                                        <i class="mdi mdi-battery-60 text-danger"></i><!-- <i class="mdi mdi-tune"></i>
                                        <i class="mdi mdi-chart-bar"></i> -->
                                    </button>
                                 </div>
                            </a>    
                        </li>
                        
                        <li class="dropdown nav-item">
                            <a class="nav-link" id="profileDropdown" href="#" data-bs-toggle="dropdown">
                                <div class="navbar-profile" >
                                    <img class="img-xs rounded-circle" src="assets/images/faces/salmon.jpg" alt="">
                                    <p class="mb-0 d-none d-sm-block navbar-profile-name">AQUARIUM PIA12</p>
                                    <i class="mdi mdi-menu-down d-none d-sm-block"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="profileDropdown">
                                <?php if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {?>
                                <?php }else{ ?>
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
                                <?php } ?>
                                <div class="dropdown-divider"></div>
                                <?php if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {?>
                                  <a class="dropdown-item preview-item" data-id="" data-bs-toggle="modal" data-bs-target="#loginModal">
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
                    <?php if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {?>
                    <?php }else{ ?>
                      <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                        <span class="mdi mdi-format-line-spacing"></span>
                      </button>
                    <?php } ?>
                </div>
            </nav>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">

                    <div class="row">
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <!--<div class="card-body"></div>-->
                            </div>
                        </div>
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h1 class="card-title">MyFishTank</h1>
                                    <div style="z-index:0;" class="owl-carousel owl-theme full-width owl-carousel-dash portfolio-carousel" id="owl-carousel-basic">
                                        <div class="item">
                                            <img src="assets/images/dashboard/acquarium.jpg" alt="">
                                        </div>
                                        <div class="item">
                                            <img src="assets/images/dashboard/acquarium1.jpg" alt="">
                                        </div>
                                        <div class="item">
                                            <img src="assets/images/dashboard/acquarium2.jpg" alt="">
                                        </div>
                                        <div class="item">
                                            <img src="assets/images/dashboard/acquarium4.jpg" alt="">
                                        </div>
                                        <div class="item">
                                            <img src="assets/images/dashboard/riallestimento0.jpg" alt="">
                                        </div>
                                        <div class="item">
                                            <img src="assets/images/dashboard/riallestimento2.jpg" alt="">
                                        </div>
                                        <div class="item">
                                            <img src="assets/images/dashboard/riallestimento3.jpg" alt="">
                                        </div> 
                                        <div class="item">
                                            <img src="assets/images/dashboard/riallestimento4.jpg" alt="">
                                        </div>
                                        <div class="item">
                                            <img src="assets/images/dashboard/riallestimento5.jpg" alt="">
                                        </div>
                                        <div class="item">
                                            <img src="assets/images/dashboard/riallestimento6.jpg" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                            <!--
                                <div>
                                  <center>
                                    <button class="btn btn-inverse-primary btn-rounded btn-icon" id="opener">
                                        <i class="mdi mdi-pulse"></i><i class= "mdi mdi-stethoscope"></i>
                                        <i class= "mdi mdi-water"></i>
                                    </button>   
                                    <button class="btn btn-inverse-success btn-rounded btn-icon" id="openFertilization">
                                        <i class="mdi mdi-eyedropper"></i><i class="mdi mdi-book-open-variant"></i>
                                        <i class="mdi mdi-cup-water"></i>
                                    </button>
                                    <button class="btn btn-inverse-danger btn-rounded btn-icon" id="openVolumes">
                                        <i class="mdi mdi-tune"></i><i class="mdi mdi-battery-60"></i>
                                        <i class="mdi mdi-chart-bar"></i>
                                    </button>
                                   </center> 
                                </div>
                            -->
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h5>EC</h5>
                                    <div class="row">
                                        <div id="chart_ec" align='center'></div>
                                        <h6 class="text-muted font-weight-normal"> <?php echo $sendEC;?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                      <h5>PH</h5> 
                                    </div>
                                    <div class="row">
                                        <div id="chart_ph" align='center'></div>
                                        <h6 class="text-muted font-weight-normal"><?php echo $sendPH;?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Temperature</h5>
                                    <div class="row">
                                        <div id="chart_temp" align='center'></div>
                                        <h6 class="text-muted font-weight-normal"><?php echo $sendT;?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--<div class="row">
                        <div class="col-md-5 col-xl-4 grid-margin stretch-card">
                            <div class="card">

                            </div>
                        </div>
                        <div class="col-md-5 col-xl-4 grid-margin stretch-card">
                            <div class="card">

                            </div>
                        </div>
                        <div class="col-md-5 col-xl-4 grid-margin stretch-card">
                            <div class="card">
                            </div>
                        </div>
                    </div>-->
                    <?php
                    	$dataNow = date('Y-m-d'); 
                    ?>
                    <div class="row">
                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">EC History</h4>
                                    <p>
                                        <input type="text" name="datepicker_ec" class="text-center" id="datepicker_ec" placeholder="<?php echo "$dataNow" ?>" />
                                        <input type="button" name="filter_ec" id="filter_ec" value="Filter" class="btn btn-info" />
                                    </p>
                                    <div class="table-responsive" style="hight: 100%;">
                                        <table id="table_id" class="table" style="width:100%;">
                                          <thead>
                                          <tr>
                                            <th>ID</th>
                                            <th>Time</th>
                                            <th>EC</th>
                                          </tr>
                                          </thead>
                                          <?php if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {?>
                                                        
                                          <?php }else{ ?> 
                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th><a href="#!" data-id="'.$row['id'].'" class="btn btn-primary addConductivity" ><i class="mdi mdi-table-row-plus-after"></i></a></th>
                                                    <th><input type="number" step="any" class="form-control text-center" id="addECField" name="po4"></th>
                                                </tr>
                                            </tfoot>
                                        <?php } ?>     
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body" style="hight: 100%;">
                                    <canvas id="areaChartEC" style="hight: 100%;" ></canvas>
                                    <center>
                                      <button type="button" id="changeEC7D" class="btn btn-primary btn-sm">7 Days</button>
                                      <button1 type="button" class="btn btn-primary btn-sm" id="changeEC1M">1 Month</button1>
                                      <button2 type="button" class="btn btn-primary btn-sm" id="changeEC2M">2 Months</button2>
                                      <button3 type="button" class="btn btn-primary btn-sm" id="changeECALL">All</button3>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">PH History</h4>
                                    <p>
                                        <input type="text" name="datepicker_ph" id="datepicker_ph" class="text-center" placeholder="<?php echo "$dataNow" ?>" />
                                        <input type="button" name="filter_ph" id="filter_ph" value="Filter" class="btn btn-info" />
                                    </p>
                                    <div class="table-responsive" style="hight: 100%;">
                                        <table id="ph_history" class="table " style="width:100%;">
                                          <thead>
                                          <tr>
                                            <th>ID</th>
                                            <th>Time</th>
                                            <th>Ph</th>
                                          </tr>
                                          </thead>
                                          <tbody>
                                          </tbody>
                                          <?php if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {?>
                                                        
                                          <?php }else{ ?> 
                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th><a href="#!" data-id="'.$row['id'].'" class="btn btn-primary addPH" ><i class="mdi mdi-table-row-plus-after"></i></a></th>
                                                    <th><input type="number" step="any" class="form-control text-center" id="addPHFieldP" name="addPHFieldP"></th>
                                                </tr>
                                             </tfoot>
                                          <?php } ?>  
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <canvas id="areaChartPH" style="height:250px"></canvas>
                                    <center> 
                                      <button type="button" id="changePH7D" class="btn btn-primary btn-sm">7 Days</button>
                                      <button1 type="button" class="btn btn-primary btn-sm" id="changePH1M">1 Month</button1>
                                      <button2 type="button" class="btn btn-primary btn-sm" id="changePH2M">2 Months</button2>
                                      <button3 type="button" class="btn btn-primary btn-sm" id="changePHALL">All</button3>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">TEMPERATURE History</h4>
                                    <p>
                                        <input type="text" name="datepicker_t" class="text-center" id="datepicker_t" placeholder="<?php echo "$dataNow" ?>" />
                                        <input type="button" name="filter_t" id="filter_t" value="Filter" class="btn btn-info" />
                                    </p>
                                    <div class="table-responsive" style="hight: 100%;">
                                        <table id="temperature_history" class="table"style="width:100%">
                                          <thead>
                                          <tr>
                                            <th>ID</th>
                                            <th>Time</th>
                                            <th>Temperature</th>
                                          </tr>
                                          </thead>
                                          <?php if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {?>
                                                        
                                          <?php }else{ ?> 
                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th><a href="#!" data-id="'.$row['id'].'" class="btn btn-primary addTemperature" ><i class="mdi mdi-table-row-plus-after"></i></a></th>
                                                    <th><input type="number" step="any" class="form-control text-center" id="addTField" name="temperature"></th>
                                                </tr>
                                            </tfoot>
                                          <?php } ?> 
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <canvas id="areaChartT"> </canvas>
                                    <center>
                                      <button type="button" id="changeT7D" class="btn btn-primary btn-sm">7 Days</button>
                                      <button1 type="button" class="btn btn-primary btn-sm" id="changeT1M">1 Month</button1>
                                      <button2 type="button" class="btn btn-primary btn-sm" id="changeT2M">2 Months</button2>
                                      <button3 type="button" class="btn btn-primary btn-sm" id="changeTALL">All</button3>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
               <!--  -->
                    <div class="row">
                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <canvas id="areaChartJoin"> </canvas>
                                    <center>
                                      <button type="button" class="btn btn-primary btn-sm" id="changeJoin7D">7 Days</button>
                                      <button1 type="button" class="btn btn-primary btn-sm" id="changeJoin1M">1 Month</button1>
                                      <button2 type="button" class="btn btn-primary btn-sm" id="changeJoin2M">2 Months</button2>
                                      <button3 type="button" class="btn btn-primary btn-sm" id="changeJoinALL">All</button3>
                                    </center>
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
                                  <a href="https://github.com/">Github</a><br />
                                  <a href="https://www.linkedin.com/in/">LinkedIn</a><br />
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

    <script src="assets/js/jquery.cookie.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <!--<script src="assets/js/data-picker.js"></script>-->
    <!--<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.3.1.js"></script> -->
   <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.0.js"></script> 
  
   <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
 
   <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
   <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
   <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
   <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
   <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
   <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
   <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
   <script src="assets/vendors/owl-carousel-2/owl.carousel.min.js"></script>

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
    
    <script src="js/ec_chart.js"></script>
    <script src="js/ph_chart.js"></script>
    <script src="js/t_chart.js"></script>
    <script src="js/ec_ph_temperature_join_chart.js"></script>
    <script src="js/dayHistoryValuesTableManaging.js"></script>
    
    <script src="js/fertilizationTable.js"></script>
    <script src="js/waterValuesTable.js"></script>
    <script src="js/loginManaging.js"></script>
    
    <script type="text/javascript">
    	
    </script>

    <script>
          google.charts.load('current', {'packages':['gauge', 'corechart']});
          //google.load('visualization', '1', {packages: ['corechart', 'gauge']});
          google.charts.setOnLoadCallback(drawCharts);

          function drawCharts() {

           var w = $(window).width();
            var x = Math.floor(w * 0.23);
            //console.log("width: " + w + ", x = " + x);
            var h = $(window).height();
            var y = Math.floor(h * 0.3);
            //console.log("height: " + h + ", y = " + y);
            
            var dataEC = google.visualization.arrayToDataTable([
                ['Label', 'Value'],
                ['µS/cm', <?php echo $ec;?>],
            ]);
             var optionsEC = {
                yellowFrom: 600, yellowTo: 820,
                redFrom: 820, redTo: 1000,
                minorTicks: 10,
                max: 1000,
                height: y,
                width: x
            };   
            
            var chartE = new google.visualization.Gauge(document.getElementById('chart_ec'));
            
            chartE.clearChart();
            chartE.draw(dataEC, optionsEC);
            
            var dataTemp = google.visualization.arrayToDataTable([
              ['Label', 'Value'],
              ['Gradi °', <?php echo $temperature;?>],
            ]);

            var dataPH = google.visualization.arrayToDataTable([
                ['Label', 'Value'],
                ['pH', <?php echo $ph;?>],
            ]);

           var options = {
                yellowFrom: 28, yellowTo: 34,
                redFrom: 34, redTo: 45,
                minorTicks: 5,
                max: 45,
                height: y,
                width: x
            };

            var optionsPH = {
                yellowFrom: 0, yellowTo: 6.5,
                greenFrom: 6.5, greenTo: 7.3,
                redFrom: 7.3, redTo: 12,
                minorTicks: 0,
                max: 12,
                height: y,
                width: x
            };
              
            var chartT = new google.visualization.Gauge(document.getElementById('chart_temp'));

            var chartP = new google.visualization.Gauge(document.getElementById('chart_ph'));
                chartT.clearChart();
                chartT.draw(dataTemp, options);
              
                chartP.clearChart();
                chartP.draw(dataPH, optionsPH);
           
        }
          
        //create trigger to resizeEnd event     
        function resize () {
            var w = $(window).width();
            var x = Math.floor(w * 0.23);
            //console.log("width: " + w + ", x = " + x);
            var h = $(window).height();
            var y = Math.floor(h * 0.3);
            //console.log("height: " + h + ", y = " + y);
            
            var dataEC = google.visualization.arrayToDataTable([
                ['Label', 'Value'],
                ['µS/cm', <?php echo $ec;?>],
            ]);
             var optionsEC = {
                yellowFrom: 600, yellowTo: 820,
                redFrom: 820, redTo: 1000,
                minorTicks: 10,
                max: 1000,
                height: y,
                width: x
            };   
            
            var chartE = new google.visualization.Gauge(document.getElementById('chart_ec'));
            
            chartE.clearChart();
            chartE.draw(dataEC, optionsEC);
            
            var dataTemp = google.visualization.arrayToDataTable([
              ['Label', 'Value'],
              ['Gradi °', <?php echo $temperature;?>],
            ]);
            
            var dataPH = google.visualization.arrayToDataTable([
                ['Label', 'Value'],
                ['pH', <?php echo $ph;?>],
            ]);

           var options = {
                yellowFrom: 28, yellowTo: 34,
                redFrom: 34, redTo: 45,
                minorTicks: 5,
                max: 45,
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
              
              var chartT = new google.visualization.Gauge(document.getElementById('chart_temp'));

              var chartP = new google.visualization.Gauge(document.getElementById('chart_ph'));
              chartT.clearChart();
              chartT.draw(dataTemp, options);
              
              chartP.clearChart();
              chartP.draw(dataPH, optionsPH);
        }

        window.onload = resize;
        window.onresize = resize;
    </script> 
       
    <!-- End custom js for this page -->
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
        xmlhttp.open("GET","php/FertilizationVolumes/getFertilizationVolumes.php",true);
        xmlhttp.send();
      }
    </script>

    
    <script type="text/javascript">
        var windowWidth = $(window).width();
        var windowHeight = $(window).height();
        
		$(document).ready( function() {
			$( "#dialog" ).dialog({
			  autoOpen: false,
              position: { my: "center", at: "bottom" },
              width: (windowWidth * 90 /100),
              //modal: true,
              title: "Water Values", 
			  show: {
				effect: "blind",
				duration: 1000
			  },
			  hide: {
				effect: "explode",
				duration: 1000
			  }
			});
            
            $("#FertilizationT").dialog({
              //modal: true,
			  autoOpen: false,
              width: (windowWidth * 90 /100),
              position: { my: "center", at: "bottom" },
              title: "Fertilization Diary", 
              
              show: {
				effect: "blind",
				duration: 1000
			  },
			  hide: {
				effect: "explode",
				duration: 1000
			  }
			});
            
            $("#VolumesO").dialog({
			  autoOpen: false,
              //modal: true,
              position: { my: "center", at: "center" },
              //buttons: {  
              //    X: function() {$(this).dialog("close");}  
              // },  
              title: "Products consumption",  
              width: (windowWidth * 90 /100), 
			  show: {
				effect: "blind",
				duration: 1000
			  },
			  hide: {
				effect: "explode",
				duration: 1000
			  }
			});
		 
			$("#opener").on("click", function() {
			  $("#dialog").dialog("open").dialog('option', 'position', 'center');
			});
            $("#openFertilization").on("click", function() {
			  $("#FertilizationT").dialog("open").dialog('option', 'position', 'center');
			});
            $("#openVolumes").on("click", function() {
              showVolumes();
			  $("#VolumesO").dialog("open").dialog('option', 'position', 'center');
			});
		});
    </script>
    

<!-- Login Modal -->    
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
          	<div class="modal-header">
                    <h3 class="card-title text-left mb-3">Login</h3>
                    <button7 type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button7>
            </div>
            <div class="modal-body">         
               <form action="index.php" method="post" id="loginForm">
                 <div class="form-group">
                    <input type="text" class="form-control p_input" placeholder="Username or email" id="email" required>
                 </div>
                 <div class="form-group">
                    <input type="password" class="form-control p_input" placeholder="Password" id="password" autocomplete="off" onkeydown = "if (event.keyCode == 13)
                        document.getElementById('login').click()" required>
                 </div>
                 <div class="form-group d-flex align-items-center justify-content-between">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input type="submit" class="form-check-input"> Remember me </label>
                    </div>
                    <a href="#" class="forgot-pass">Forgot password</a>
                 </div>
                 <div class="text-center">
                    <button class="btn btn-primary btn-block enter-btn" id="login" >Log in</button>
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
    <div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div id="dialog">
            	<div class="card-body">
                  <div class="table-responsive">
                      <table id="waterValuesTable" class="table table-bordered" style="width:100%;">
                          <thead>
                              <tr>
                                  <th><p>Data</p></th>
                                  <th><p>EC_PRE</p></th>
                                  <th><p>EC_AFT</p></th>
                                  <th> <p>&nbsp  &nbsp &nbsp PH &nbsp &nbsp &nbsp</p></th>
                                  <th><p>Nitriti mg/l</p></th>
                                  <th><p>Nitrati mg/l</p></th>
                                  <th><p>GH dGH</p></th>
                                  <th><p>KH dKH</p></th>
                                  <th><p>Fosfati mg/l</p></th>
                                  <th><p>Options</p></th>
                              </tr>
                          </thead>
                          <tbody>
                          </tbody>
                          <?php if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {?>

                          <?php }else{ ?> 
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th><input type="number" class="form-control text-center" id="addecPField" name="ecP"></th>
                                    <th><input type="number" class="form-control text-center" id="addecAField" name="ecA"></th>
                                    <th><input type="number" class="form-control text-center" id="addphField" name="ph"></th>
                                    <th><input type="number" class="form-control text-center" id="addNo2Field" name="no2"></th>
                                    <th><input type="number" class="form-control text-center" id="addNo3Field" name="no3"></th>
                                    <th><input type="number" class="form-control text-center" id="addGHField" name="gh"></th>
                                    <th><input type="number" class="form-control text-center" id="addKHField" name="kh"></th>
                                    <th><input type="number" class="form-control text-center" id="addPo4Field" name="po4"></th>
                                    <th><a href="#!" data-id="'.$row['id'].'" class="btn btn-primary addBtn" ><i class="mdi mdi-table-row-plus-after"></i></a></th>      
                                </tr>
                            </tfoot>
                          <?php } ?>
                      </table>
                  </div>
        	    </div>
            </div>
	</div>    
    <div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div id="FertilizationT">
           		<div class="card-body">
                  <div class="table-responsive">
                      <table id="fertilizationTable" class="table table-bordered" style="width:100%;">
                          <thead>
                              <tr>
                                  <th><p>Data</p></th>
                                  <th><p>Potassio ml</p></th>
                                  <th><p>Magnesio ml</p></th>
                                  <th><p>Ferro ml</p></th>
                                  <th><p>Rinverdente ml</p></th>
                                  <th><p>Fosforo ml</p></th>
                                  <th><p>Azoto ml</p></th>
                                  <th><p>&nbsp NPK pz &nbsp</p></th>
                                  <th><p>Options</p></th>
                              </tr>
                          </thead>
                          <tbody>
                          </tbody>
                          <?php if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {?>

                          <?php }else{ ?>
                            <tfoot>
                              <tr>
                                  <th></th>
                                  <th><input type="text"  class="form-control text-center" id="addKField" name="potassio"></th>
                                  <th><input type="text"  class="form-control text-center" id="addMgField" name="magnesio"></th>
                                  <th><input type="text"  class="form-control text-center" id="addFeField" name="ferro"></th>
                                  <th><input type="text"  class="form-control text-center" id="addRinverdenteField" name="rinverdente"></th>
                                  <th><input type="text"  class="form-control text-center" id="addPField" name="fosforo"></th>
                                  <th><input type="text"  class="form-control text-center" id="addNField" name="azoto"></th>
                                  <th><input type="text"  class="form-control text-center" id="addNPKField" name="npk"></th>
                                  <th><a href="#!" data-id="'.$row['id'].'" class="btn btn-primary addFertilizationBtn" ><i class="mdi mdi-table-row-plus-after"></i></a></th>
                              </tr>
                            </tfoot>
                          <?php } ?>
                      </table>
                  </div>
            	</div>
            </div>
		
	</div>     
    <div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    	<div class="row ">
        <div class="col-4 grid-margin">
        <div class="card">
        	<div class="card-body">
        		<div id="VolumesO"  >
					<div id="volumes">
                    </div>
		        </div>
            </div>
        </div></div>
</div>	</div>      
</body>

</html>
