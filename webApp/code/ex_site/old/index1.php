<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PIA12 FISH TANK - AQUARIUM</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.theme.default.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gauge.js/1.3.5/gauge.min.js"></script>
  
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/salmon.png" />
</head>

<body class="sidebar-icon-only">
    <div class="container-scroller">
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <!--<div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
                <a class="sidebar-brand brand-logo" href="index.html"><img src="assets/images/logo.svg" alt="logo" /></a>
                <a class="sidebar-brandbrand-logo-mini" href="index.html"><img src="assets/images/logo-mini.svg" alt="logo" /></a>
            </div>-->
            <ul class="nav">
                <!--<li class="nav-item profile">
                    <div class="profile-desc">
                        <div class="profile-pic">
                            <div class="count-indicator">
                                <img class="img-xs rounded-circle " src="assets/images/faces/face15.jpg" alt="">
                                <span class="count bg-success"></span>
                            </div>
                            <div class="profile-name">
                                <h5 class="mb-0 font-weight-normal">Henry Klein</h5>
                                <span>Gold Member</span>
                            </div>
                        </div>
                        <a href="#" id="profile-dropdown" data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
                        <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list" aria-labelledby="profile-dropdown">
                            <a href="#" class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-dark rounded-circle">
                                        <i class="mdi mdi-settings text-primary"></i>
                                    </div>
                                </div>
                                <div class="preview-item-content">
                                    <p class="preview-subject ellipsis mb-1 text-small">Account settings</p>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-dark rounded-circle">
                                        <i class="mdi mdi-onepassword  text-info"></i>
                                    </div>
                                </div>
                                <div class="preview-item-content">
                                    <p class="preview-subject ellipsis mb-1 text-small">Change Password</p>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-dark rounded-circle">
                                        <i class="mdi mdi-calendar-today text-success"></i>
                                    </div>
                                </div>
                                <div class="preview-item-content">
                                    <p class="preview-subject ellipsis mb-1 text-small">To-do list</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </li>-->
                <li class="nav-item nav-category">
                    <span class="nav-link">Navigation</span>
                </li>
                <li class="nav-item menu-items">
                    <a class="nav-link" href="index.html">
                        <span class="menu-icon">
                <i class="mdi mdi-speedometer"></i>
              </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item menu-items">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                        <span class="menu-icon">
                <i class="mdi mdi-laptop"></i>
              </span>
                        <span class="menu-title">Basic UI Elements</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="ui-basic">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a></li>
                            <li class="nav-item"> <a class="nav-link" href="pages/ui-features/dropdowns.html">Dropdowns</a></li>
                            <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a></li>
                            <li class="nav-item"> <a class="nav-link" href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addConductivityModal">Add EC</a></li>
                            <li class="nav-item"> <a class="nav-link" href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addPHModal">Add PH</a></li>
                            <li class="nav-item"> <a class="nav-link" href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addTemperatureModal">Add Temperature</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item menu-items">
                    <a class="nav-link" href="pages/forms/basic_elements.html">
                        <span class="menu-icon">
                <i class="mdi mdi-playlist-play"></i>
              </span>
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
                        <span class="menu-icon">
                <i class="mdi mdi-chart-bar"></i>
              </span>
                        <span class="menu-title">Charts</span>
                    </a>
                </li>
                <li class="nav-item menu-items">
                    <a class="nav-link" href="pages/icons/mdi.html">
                        <span class="menu-icon">
                <i class="mdi mdi-contacts"></i>
              </span>
                        <span class="menu-title">Icons</span>
                    </a>
                </li>
                <li class="nav-item menu-items">
                    <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                        <span class="menu-icon">
                <i class="mdi mdi-security"></i>
              </span>
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
                        <span class="menu-icon">
                <i class="mdi mdi-file-document-box"></i>
              </span>
                        <span class="menu-title">Documentation</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_navbar.html -->
            <nav class="navbar p-0 fixed-top d-flex flex-row">
                <!--<div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
                    <a class="navbar-brand brand-logo-mini" href="index.html"><img src="assets/images/logo-mini.svg" alt="logo" /></a>
                </div>-->
                <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="mdi mdi-menu"></span>
                    </button>
                    <ul class="navbar-nav navbar-nav-right">
                        <!--<li class="nav-item dropdown d-none d-lg-block">
                            <a class="nav-link btn btn-success create-new-button" id="createbuttonDropdown" data-bs-toggle="dropdown" aria-expanded="false" href="#">Add Measurements</a>
                            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="createbuttonDropdown">
                                <h6 class="p-3 mb-0">Measurements</h6>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item preview-item">
                                    <div class="preview-thumbnail">
                                        <div class="preview-icon bg-dark rounded-circle">
                                            <i class="mdi mdi-file-outline text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="preview-item-content">
                                        <p class="preview-subject ellipsis mb-1">Temperature</p>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item preview-item">
                                    <div class="preview-thumbnail">
                                        <div class="preview-icon bg-dark rounded-circle">
                                            <i class="mdi mdi-web text-info"></i>
                                        </div>
                                    </div>
                                    <div class="preview-item-content">
                                        <p class="preview-subject ellipsis mb-1">EC/TDS</p>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item preview-item">
                                    <div class="preview-thumbnail">
                                        <div class="preview-icon bg-dark rounded-circle">
                                            <i class="mdi mdi-layers text-danger"></i>
                                        </div>
                                    </div>
                                    <div class="preview-item-content">
                                        <p class="preview-subject ellipsis mb-1">PH</p>
                                    </div>
                                </a>
                            </div>
                        </li>-->
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
                                            <i class="mdi mdi-settings text-success"></i>
                                        </div>
                                    </div>
                                    <div class="preview-item-content">
                                        <p class="preview-subject mb-1">Settings</p>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item preview-item">
                                    <div class="preview-thumbnail">
                                        <div class="preview-icon bg-dark rounded-circle">
                                            <i class="mdi mdi-logout text-danger"></i>
                                        </div>
                                    </div>
                                    <div class="preview-item-content">
                                        <p class="preview-subject mb-1">Log out</p>
                                    </div>
                                </a>
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
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <!--<div class="card-body"></div>-->
                            </div>
                        </div>
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="card-title">MyFishTank</h3>
                                    <div class="owl-carousel owl-theme full-width owl-carousel-dash portfolio-carousel" id="owl-carousel-basic">
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
                                            <img src="assets/images/dashboard/acquarium3.jpg" alt="">
                                        </div>
                                        <div class="item">
                                            <img src="assets/images/dashboard/acquarium4.jpg" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <!--<div class="card-body"></div>-->
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h5>EC: <span id="gauge-value_ec"></span> µS/cm </h5>
                                    <div class="row">
                                        <canvas style="width: 100%;" id="ec_gauge"></canvas>
                                        <h6 class="text-muted font-weight-normal"> 9.61% Since last month</h6>
                                        <!--</div>
                                        <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                                            <i class="icon-lg mdi mdi-wallet-travel text-danger ms-auto"></i>
                                        </div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h5>PH: <span id="gauge-value_ph"></span></h5>
                                    <div class="row">
                                        <canvas style="width: 100%;" id="ph_gauge"></canvas>
                                        <h6 class="text-muted font-weight-normal">2.27% Since last month</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Temperature: <span id="gauge-value_temp"></span> C°</h5>
                                    <div class="row">
                                        <div id="chart_ec"></div>
                                        <canvas style="width: 100%;" id="temperature_gauge"></canvas>
                                        <h6 class="text-muted font-weight-normal">11.38% Since last month</h6>
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

                    <div class="row">
                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">EC History</h4>
                                    <p>
                                        <input type="text" name="datepicker_ec" id="datepicker_ec" placeholder="2022-04-22" />
                                        <input type="button" name="filter_ec" id="filter_ec" value="Filter" class="btn btn-info" />
                                    </p>
                                    <div class="table-responsive" style="hight: 100%;">
                                        <table id="table_ec_history" class="table">
                                            <thead>
                                                <tr>
                                                    <th> Time </th>
                                                    <th> EC </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-right"> 15:00:00 </td>
                                                    <td class="text-right font-weight-medium"> 26.35 </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"> 08:00:00 </td>
                                                    <td class="text-right font-weight-medium"> 33.25 </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"> 08:00:00 </td>
                                                    <td class="text-right font-weight-medium"> 33.25 </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"> 08:00:00 </td>
                                                    <td class="text-right font-weight-medium"> 33.25 </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"> 08:00:00 </td>
                                                    <td class="text-right font-weight-medium"> 33.25 </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"> 07:06:00 </td>
                                                    <td class="text-right font-weight-medium"> 15.45 </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"> 04:50:00 </td>
                                                    <td class="text-right font-weight-medium"> 25.00 </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <canvas id="areaChartEC" style="height:250px"></canvas>
                                    <button type="button" id="changeEC7D" class="btn btn-primary btn-fw">7 Days</button>
                                    <button1 type="button" class="btn btn-primary btn-fw" id="changeEC1M">1 Month</button1>
                                    <button2 type="button" class="btn btn-primary btn-fw" id="changeEC2M">2 Months</button2>
                                    <button3 type="button" class="btn btn-primary btn-fw" id="changeECALL">All</button3>
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
                                        <input type="text" name="datepicker_ph" id="datepicker_ph" placeholder="2022-04-22" />
                                        <input type="button" name="filter_ph" id="filter_ph" value="Filter" class="btn btn-info" />
                                    </p>
                                    <div class="table-responsive">
                                        <table id="table_ph_history" class="table">
                                            <thead>
                                                <tr>
                                                    <th> Time </th>
                                                    <th> PH </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-right"> 15:00:00 </td>
                                                    <td class="text-right font-weight-medium"> 26.35 </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"> 08:00:00 </td>
                                                    <td class="text-right font-weight-medium"> 33.25 </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"> 07:06:00 </td>
                                                    <td class="text-right font-weight-medium"> 15.45 </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"> 04:50:00 </td>
                                                    <td class="text-right font-weight-medium"> 25.00 </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <canvas id="areaChartPH" style="height:250px"></canvas>
                                    <button type="button" id="changePH7D" class="btn btn-primary btn-fw">7 Days</button>
                                    <button1 type="button" class="btn btn-primary btn-fw" id="changePH1M">1 Month</button1>
                                    <button2 type="button" class="btn btn-primary btn-fw" id="changePH2M">2 Months</button2>
                                    <button3 type="button" class="btn btn-primary btn-fw" id="changePHALL">All</button3>
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
                                        <input type="text" name="datepicker_t" id="datepicker_t" placeholder="2022-04-22" />
                                        <input type="button" name="filter_t" id="filter_t" value="Filter" class="btn btn-info" />
                                    </p>
                                    <div class="table-responsive">
                                        <table id="table_t_history" class="table">
                                            <thead>
                                                <tr>
                                                    <th> Time </th>
                                                    <th> Temperature </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-right"> 15:00:00 </td>
                                                    <td class="text-right font-weight-medium"> 26.35 </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"> 08:00:00 </td>
                                                    <td class="text-right font-weight-medium"> 33.25 </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"> 07:06:00 </td>
                                                    <td class="text-right font-weight-medium"> 15.45 </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"> 04:50:00 </td>
                                                    <td class="text-right font-weight-medium"> 25.00 </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <canvas id="areaChartT"> </canvas>
                                    <button type="button" id="changeT7D" class="btn btn-primary btn-fw">7 Days</button>
                                    <button1 type="button" class="btn btn-primary btn-fw" id="changeT1M">1 Month</button1>
                                    <button2 type="button" class="btn btn-primary btn-fw" id="changeT2M">2 Months</button2>
                                    <button3 type="button" class="btn btn-primary btn-fw" id="changeTALL">All</button3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © bootstrapdash.com 2021</span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"> Free <a href="https://www.bootstrapdash.com/bootstrap-admin-template/" target="_blank">Bootstrap admin template</a> from Bootstrapdash.com</span>
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
    <script type="text/javascript" charset="utf8" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
    <script>
        $(function() {
            $("#table_ec_history").DataTable({
                "fnCreatedRow": function(nRow, aData, iDataIndex) {
                    $(nRow).attr('id', aData[0]);
                },
                "bInfo": true,
                "bJQueryUI": true,
                "bProcessing": true,
                "bPaginate": true,
                "iDisplayLength": 4,
                "sPaginationType": "two_button",
                "sDom": 'rtip',
                "columnDefs": [{
                    'target': [5],
                    'orderable': false,
                }]
            });
        });
        $(function() {
            $("#table_ph_history").DataTable({
                "fnCreatedRow": function(nRow, aData, iDataIndex) {
                    $(nRow).attr('id', aData[0]);
                },
                "bInfo": true,
                "bJQueryUI": true,
                "bProcessing": true,
                "bPaginate": true,
                "iDisplayLength": 4,
                "sPaginationType": "two_button",
                "sDom": 'rtip',
                "columnDefs": [{
                    'target': [5],
                    'orderable': false,
                }]
            });
        });
        $(function() {
            $("#table_t_history").DataTable({
                "fnCreatedRow": function(nRow, aData, iDataIndex) {
                    $(nRow).attr('id', aData[0]);
                },
                "bInfo": true,
                "bJQueryUI": true,
                "bProcessing": true,
                "bPaginate": true,
                "iDisplayLength": 4,
                "sPaginationType": "two_button",
                "sDom": 'rtip',
                "columnDefs": [{
                    'target': [5],
                    'orderable': false,
                }]
            });
        });
    </script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script src="assets/js/data-picker.js"></script>

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
    <script src="assets/js/charts.js"></script>
    <script>
    <?php
          function calculate_median($arr) {
            $count = count($arr); //total numbers in array
            $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
            if($count % 2) { // odd number, middle is the median
              $median = $arr[$middleval];
            } else { // even number, calculate avg of 2 medians
              $low = $arr[$middleval];
              $high = $arr[$middleval+1];
              $median = (($low+$high)/2);
            }
            return $median;
          }
          
          function calculate_average($arr) {
            $count = count($arr); //total numbers in array
            foreach ($arr as $value) {
              $total = $total + $value; // total value of array numbers
            }
            $average = ($total/$count); // get average value
            return $average;
          }
          
          include("assets/php/connection.php");
          if ($con->connect_error) {
            die("Connection failed: " . $con->connect_error);
          }
          $dataNow1 = date('Y-m-d');  
          $sqlT = "SELECT temperature FROM my_myfishtank.temp_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') = '$dataNow1' ORDER BY data_send";
          $sqlEC = "SELECT ec FROM my_myfishtank.ec_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') = '$dataNow1' ORDER BY data_send";
          $sqlPH = "SELECT ph FROM my_myfishtank.ph_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') = '$dataNow1' ORDER BY data_send";

          $resultT = $con->query($sqlT);
          $resultEC = $con->query($sqlEC);
          $resultPH = $con->query($sqlPH);
          
          $t_array = array();
          if ($resultT->num_rows > 0) {
            // output data of each row
            while($rowT = $resultT->fetch_assoc()) {
              $t_array[] = $rowT["temperature"];
              $sendT = $rowT["send"];
            }
          } else {
            $sendT = "no data";
          }
          $temperature = calculate_median($t_array);
          
          $ec_array = array();
          if ($resultEC->num_rows > 0) {
            // output data of each row
            while($rowEC = $resultEC->fetch_assoc()) {
              $ec_array[] = $rowEC["ec"];
              $sendEC = $rowEC["send"];
            }
          } else {
            $sendEC = "no data";
          }
          $ec = calculate_median($ec_array);
          
          $ph_array = array();
          if ($resultPH->num_rows > 0) {
            // output data of each row
            while($rowPH = $resultPH->fetch_assoc()) {
              $ph_array[] = $rowPH["ph"];
              $sendPH = $rowPH["send"];
            }
          } else {
            $sendPH = "no data";
          }
          $ph = calculate_median($ph_array);
          echo $ec."\n";
          echo $ph."\n";
          echo $temperature."\n";
          $con->close();
    ?>

          google.load('visualization', '1', {packages: ['corechart', 'gauge']});
          google.charts.setOnLoadCallback(drawChart);

          function drawChart() {

            var dataTemp = google.visualization.arrayToDataTable([
              ['Label', 'Value'],
              ['Gradi °', <?php echo $temperature;?>],
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
    <!-- End custom js for this page -->

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
                            <label for="addKeyField" class="col-md-3 form-label">Key</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" id="addKeyField" name="key">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="addTField" class="col-md-3 form-label">Temperature</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="addTField" name="temperature">
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
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
                            <label for="addKeyField" class="col-md-3 form-label">Key</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" id="addKeyField" name="key">
                            </div>
                        </div>
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
                    <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
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
                            <label for="addKeyField" class="col-md-3 form-label">Key</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" id="addKeyField" name="key">
                            </div>
                        </div>
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
                    <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
                </div>
            </div>
        </div>
    </div>
</body>

</html>