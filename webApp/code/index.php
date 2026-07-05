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
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
   
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/glassmorphism.css">
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

       /* Dropdown "AQUARIUM PIA12" - sfondo opaco bluetto */
       .navbar-dropdown.dropdown-menu {
           background-color: #0d2a3a !important;
           border: 1px solid #1a4a62 !important;
           box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6) !important;
           backdrop-filter: none !important;
           -webkit-backdrop-filter: none !important;
           opacity: 1 !important;
       }

       .navbar-dropdown.dropdown-menu h6 {
           color: #7ecfea !important;
       }

       .navbar-dropdown.dropdown-menu .dropdown-item:hover,
       .navbar-dropdown.dropdown-menu .dropdown-item:focus {
           background-color: #1a4a62 !important;
       }

       .navbar-dropdown.dropdown-menu .dropdown-divider {
           border-color: #1a4a62 !important;
       }

       .navbar-dropdown.dropdown-menu .preview-subject {
           color: #c8e8f5 !important;
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

                                    <!-- ── Aquarium Carousel ────────────────────────────── -->
                                    <style>
                                        #aqCarousel {
                                            position: relative;
                                            width: 100%;
                                            border-radius: 14px;
                                            overflow: hidden;
                                            background: #000;
                                            box-shadow: 0 8px 32px rgba(0,0,0,0.45);
                                            user-select: none;
                                        }

                                        /* Main image area */
                                        #aqCarousel .aq-stage {
                                            position: relative;
                                            width: 100%;
                                            aspect-ratio: 16/9;
                                            overflow: hidden;
                                        }

                                        #aqCarousel .aq-stage img {
                                            position: absolute;
                                            inset: 0;
                                            width: 100%;
                                            height: 100%;
                                            object-fit: cover;
                                            opacity: 0;
                                            transition: opacity 0.6s ease;
                                        }

                                        #aqCarousel .aq-stage img.aq-active {
                                            opacity: 1;
                                            z-index: 1;
                                        }

                                        /* Arrow buttons */
                                        #aqCarousel .aq-arrow {
                                            position: absolute;
                                            top: 50%;
                                            transform: translateY(-50%);
                                            z-index: 10;
                                            background: rgba(0,0,0,0.45);
                                            border: none;
                                            color: #fff;
                                            width: 36px;
                                            height: 36px;
                                            border-radius: 50%;
                                            font-size: 18px;
                                            line-height: 1;
                                            cursor: pointer;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            transition: background 0.2s, transform 0.2s;
                                            backdrop-filter: blur(4px);
                                        }
                                        #aqCarousel .aq-arrow:hover {
                                            background: rgba(0,180,200,0.7);
                                            transform: translateY(-50%) scale(1.12);
                                        }
                                        #aqCarousel .aq-prev { left: 10px; }
                                        #aqCarousel .aq-next { right: 10px; }

                                        /* Counter top-right */
                                        #aqCarousel .aq-counter {
                                            position: absolute;
                                            top: 10px;
                                            right: 12px;
                                            z-index: 10;
                                            background: rgba(0,0,0,0.5);
                                            color: #fff;
                                            font-size: 0.75rem;
                                            padding: 2px 8px;
                                            border-radius: 20px;
                                            backdrop-filter: blur(4px);
                                            letter-spacing: 0.04em;
                                        }

                                        /* Autoplay progress bar */
                                        #aqCarousel .aq-progress {
                                            position: absolute;
                                            bottom: 0;
                                            left: 0;
                                            height: 3px;
                                            width: 0%;
                                            background: rgba(0,210,230,0.85);
                                            z-index: 10;
                                            transition: width linear;
                                        }

                                        /* Dot indicators */
                                        #aqCarousel .aq-dots {
                                            display: flex;
                                            justify-content: center;
                                            gap: 6px;
                                            padding: 8px 0 4px;
                                        }
                                        #aqCarousel .aq-dot {
                                            width: 7px;
                                            height: 7px;
                                            border-radius: 50%;
                                            background: rgba(255,255,255,0.25);
                                            cursor: pointer;
                                            transition: background 0.25s, transform 0.25s;
                                            border: none;
                                            padding: 0;
                                        }
                                        #aqCarousel .aq-dot.aq-active {
                                            background: rgba(0,210,230,0.9);
                                            transform: scale(1.4);
                                        }

                                        /* Thumbnail strip */
                                        #aqCarousel .aq-thumbs {
                                            display: flex;
                                            gap: 6px;
                                            padding: 6px 8px 8px;
                                            overflow-x: auto;
                                            scrollbar-width: thin;
                                            scrollbar-color: rgba(255,255,255,0.2) transparent;
                                        }
                                        #aqCarousel .aq-thumbs img {
                                            width: 52px;
                                            height: 38px;
                                            object-fit: cover;
                                            border-radius: 6px;
                                            cursor: pointer;
                                            border: 2px solid transparent;
                                            transition: border-color 0.2s, transform 0.2s, opacity 0.2s;
                                            opacity: 0.6;
                                            flex-shrink: 0;
                                        }
                                        #aqCarousel .aq-thumbs img:hover {
                                            opacity: 1;
                                            transform: scale(1.07);
                                        }
                                        #aqCarousel .aq-thumbs img.aq-active {
                                            border-color: rgba(0,210,230,0.9);
                                            opacity: 1;
                                        }
                                    </style>

                                    <div id="aqCarousel">
                                        <div class="aq-stage">
                                            <img src="assets/images/dashboard/acquarium.jpg"      class="aq-active" alt="Aquarium 1">
                                            <img src="assets/images/dashboard/acquarium1.jpg"     alt="Aquarium 2">
                                            <img src="assets/images/dashboard/acquarium2.jpg"     alt="Aquarium 3">
                                            <img src="assets/images/dashboard/acquarium4.jpg"     alt="Aquarium 4">
                                            <img src="assets/images/dashboard/riallestimento0.jpg" alt="Riallestimento 1">
                                            <img src="assets/images/dashboard/riallestimento2.jpg" alt="Riallestimento 2">
                                            <img src="assets/images/dashboard/riallestimento3.jpg" alt="Riallestimento 3">
                                            <img src="assets/images/dashboard/riallestimento4.jpg" alt="Riallestimento 4">
                                            <img src="assets/images/dashboard/riallestimento5.jpg" alt="Riallestimento 5">
                                            <img src="assets/images/dashboard/riallestimento6.jpg" alt="Riallestimento 6">

                                            <button type="button" class="aq-arrow aq-prev" aria-label="Previous">&#8249;</button>
                                            <button type="button" class="aq-arrow aq-next" aria-label="Next">&#8250;</button>
                                            <span class="aq-counter">1 / 10</span>
                                            <div class="aq-progress" id="aqProgress"></div>
                                        </div>

                                        <div class="aq-dots" id="aqDots"></div>

                                        <div class="aq-thumbs" id="aqThumbs">
                                            <img src="assets/images/dashboard/acquarium.jpg"      class="aq-active" alt="1">
                                            <img src="assets/images/dashboard/acquarium1.jpg"     alt="2">
                                            <img src="assets/images/dashboard/acquarium2.jpg"     alt="3">
                                            <img src="assets/images/dashboard/acquarium4.jpg"     alt="4">
                                            <img src="assets/images/dashboard/riallestimento0.jpg" alt="5">
                                            <img src="assets/images/dashboard/riallestimento2.jpg" alt="6">
                                            <img src="assets/images/dashboard/riallestimento3.jpg" alt="7">
                                            <img src="assets/images/dashboard/riallestimento4.jpg" alt="8">
                                            <img src="assets/images/dashboard/riallestimento5.jpg" alt="9">
                                            <img src="assets/images/dashboard/riallestimento6.jpg" alt="10">
                                        </div>
                                    </div>

                                    <script>
                                    (function () {
                                        const AUTOPLAY_MS  = 4000;
                                        const slides       = document.querySelectorAll('#aqCarousel .aq-stage > img');
                                        const thumbs       = document.querySelectorAll('#aqThumbs img');
                                        const counter      = document.querySelector('#aqCarousel .aq-counter');
                                        const progress     = document.getElementById('aqProgress');
                                        const dotsWrap     = document.getElementById('aqDots');
                                        const total        = slides.length;
                                        let current        = 0;
                                        let autoplayTimer  = null;
                                        let progressTimer  = null;

                                        // Build dots
                                        for (let i = 0; i < total; i++) {
                                            const d = document.createElement('button');
                                            d.type = 'button'; // evita submit su form padre
                                            d.className = 'aq-dot' + (i === 0 ? ' aq-active' : '');
                                            d.setAttribute('aria-label', 'Slide ' + (i + 1));
                                            d.addEventListener('click', () => goTo(i));
                                            dotsWrap.appendChild(d);
                                        }
                                        const dots = dotsWrap.querySelectorAll('.aq-dot');

                                        function goTo(idx) {
                                            slides[current].classList.remove('aq-active');
                                            thumbs[current].classList.remove('aq-active');
                                            dots[current].classList.remove('aq-active');

                                            current = (idx + total) % total;

                                            slides[current].classList.add('aq-active');
                                            thumbs[current].classList.add('aq-active');
                                            dots[current].classList.add('aq-active');
                                            counter.textContent = (current + 1) + ' / ' + total;

                                            // Scroll thumb into view — solo orizzontalmente nel contenitore, senza muovere la pagina
                                            const thumbsEl = document.getElementById('aqThumbs');
                                            const th = thumbs[current];
                                            const containerLeft = thumbsEl.scrollLeft;
                                            const containerRight = containerLeft + thumbsEl.clientWidth;
                                            const thumbLeft = th.offsetLeft;
                                            const thumbRight = thumbLeft + th.offsetWidth;
                                            if (thumbLeft < containerLeft) {
                                                thumbsEl.scrollLeft = thumbLeft - 6;
                                            } else if (thumbRight > containerRight) {
                                                thumbsEl.scrollLeft = thumbRight - thumbsEl.clientWidth + 6;
                                            }

                                            resetProgress();
                                        }

                                        function resetProgress() {
                                            clearTimeout(progressTimer);
                                            progress.style.transition = 'none';
                                            progress.style.width = '0%';
                                            // Force reflow
                                            progress.getBoundingClientRect();
                                            progress.style.transition = 'width ' + AUTOPLAY_MS + 'ms linear';
                                            progress.style.width = '100%';
                                        }

                                        function startAutoplay() {
                                            clearInterval(autoplayTimer);
                                            autoplayTimer = setInterval(() => goTo(current + 1), AUTOPLAY_MS);
                                        }

                                        // Arrow buttons
                                        document.querySelector('#aqCarousel .aq-prev').addEventListener('click', () => {
                                            goTo(current - 1);
                                            startAutoplay();
                                        });
                                        document.querySelector('#aqCarousel .aq-next').addEventListener('click', () => {
                                            goTo(current + 1);
                                            startAutoplay();
                                        });

                                        // Thumbnail clicks
                                        thumbs.forEach((th, i) => th.addEventListener('click', () => {
                                            goTo(i);
                                            startAutoplay();
                                        }));

                                        // Pause on hover
                                        document.getElementById('aqCarousel').addEventListener('mouseenter', () => {
                                            clearInterval(autoplayTimer);
                                            clearTimeout(progressTimer);
                                            progress.style.transition = 'none';
                                        });
                                        document.getElementById('aqCarousel').addEventListener('mouseleave', () => {
                                            resetProgress();
                                            startAutoplay();
                                        });

                                        // Swipe support (touch)
                                        let touchStartX = 0;
                                        document.querySelector('#aqCarousel .aq-stage').addEventListener('touchstart', e => {
                                            touchStartX = e.changedTouches[0].clientX;
                                        }, { passive: true });
                                        document.querySelector('#aqCarousel .aq-stage').addEventListener('touchend', e => {
                                            const diff = touchStartX - e.changedTouches[0].clientX;
                                            if (Math.abs(diff) > 40) {
                                                goTo(diff > 0 ? current + 1 : current - 1);
                                                startAutoplay();
                                            }
                                        }, { passive: true });

                                        // Keyboard support
                                        document.addEventListener('keydown', e => {
                                            if (e.key === 'ArrowLeft')  { goTo(current - 1); startAutoplay(); }
                                            if (e.key === 'ArrowRight') { goTo(current + 1); startAutoplay(); }
                                        });

                                        // Init
                                        resetProgress();
                                        startAutoplay();
                                    })();
                                    </script>
                                    <!-- ── End Aquarium Carousel ───────────────────────── -->

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
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h5 class="mb-0">EC</h5>
                                        <h6 class="text-muted mb-0"><?php echo $sendEC;?></h6>
                                    </div>
                                    <div class="row">
                                        <div id="chart_ec" align='center'></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h5 class="mb-0">PH</h5>
                                        <h6 class="text-muted mb-0"><?php echo $sendPH;?></h6>
                                    </div>
                                    <div class="row">
                                        <div id="chart_ph" align='center'></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h5 class="mb-0">Temperature</h5>
                                        <h6 class="text-muted mb-0"><?php echo $sendT;?></h6>
                                    </div>
                                    <div class="row">
                                        <div id="chart_temp" align='center'></div>
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
                                                    <th><a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary addConductivity" ><i class="mdi mdi-table-row-plus-after"></i></a></th>
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
                                                    <th><a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary addPH" ><i class="mdi mdi-table-row-plus-after"></i></a></th>
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
                                                    <th><a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary addTemperature" ><i class="mdi mdi-table-row-plus-after"></i></a></th>
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
    <script src="assets/js/jquery.cookie.js" type="text/javascript"></script>
    <!--<script type="text/javascript" src="https://www.google.com/jsapi"></script>-->
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

    <!-- Google Charts loader, necessario per google_gauge.js
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    -->
    <!-- ECharts: gauge moderni con lancetta e fasce colorate (Opzione 3)
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    -->
    <!-- Highcharts (Opzione 5) — scegli UNA delle due righe:
    A) CDN diretto (richiede internet):  -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <!--  B) Locale (funziona anche offline — scarica i file e mettili in assets/vendors/highcharts/): -->
    <!--  <script src="assets/vendors/highcharts/highcharts.js"></script> -->
    <!--  <script src="assets/vendors/highcharts/highcharts-more.js"></script> -->

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
    <!-- <script src="js/google_gauge.js"></script> -->
    <!-- <script src="js/echarts_gauge.js"></script> -->
    <script src="js/highcharts_gauge.js"></script>
    <script src="js/fertilizationTable.js"></script>
    <script src="js/waterValuesTable.js"></script>
    <script src="js/loginManaging.js"></script>
    <script src="assets/js/glassmorphism-enhancements.js"></script>
    <script type="text/javascript">
    </script>
    <script src="js/getFertilizationVolumes.js"></script>
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
                setTimeout(function() {
                    showVolumes();
                    $("#FertilizationT").dialog("open").dialog('option', 'position', 'center');
                }, 10);
            }); 
            $("#openVolumes").on("click", function() {
                // Il click finisce subito, l'operazione pesante viene rimandata di 10ms
                setTimeout(function() {
                    showVolumes();
                    $("#VolumesO").dialog("open").dialog('option', 'position', 'center');
                }, 10);
            });
        });
    </script>
    <!-- Login Modal -->    
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                        <h3 class="card-title text-left mb-3">Login</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">         
                    <form id="loginForm" onsubmit="return false;">
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
                                <input type="checkbox" class="form-check-input"> Remember me </label>
                            </div>
                            <a href="#" id="forgotPasswordLink" class="forgot-pass">Forgot password</a>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-primary btn-block enter-btn" id="login" >Log in</button>
                        </div>
                        <br />
                        <div class="d-flex">
                            <button type="button" class="btn btn-facebook me-2 col">
                            <i class="mdi mdi-facebook"></i> Facebook </button>
                            <button type="button" class="btn btn-google col">
                            <i class="mdi mdi-google-plus"></i> Google plus </button>
                        </div>
                        <div class="modal-footer">
                            <p class="sign-up">Don't have an Account?<a href="#" id="signupLink"> Sign Up</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Sign Up Modal -->
    <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="card-title text-left mb-3">Sign Up</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form id="signupForm">
                    <div class="form-group">
                        <input type="text" class="form-control p_input" placeholder="First Name" id="firstNameSignUp" required>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control p_input" placeholder="Email" id="emailSignUp" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control p_input" placeholder="Password" id="passwordSignUp" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control p_input" placeholder="Confirm Password" id="passwordConfirmSignUp" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control p_input" placeholder="Invite Code" id="inviteCode" required>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-primary btn-block enter-btn" id="signupBtn">Register</button>
                    </div>
                    <div class="modal-footer">
                        <p>Already have an Account?<a href="#" class="backToLogin"> Log In</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="card-title text-left mb-3">Forgot Password</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="forgotPasswordForm">
                        <p class="text-muted">Enter your email to receive a password reset link</p>
                        <div class="form-group">
                            <input type="email" class="form-control p_input" placeholder="Email" id="emailForgot" required>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-primary btn-block enter-btn" id="forgotPasswordBtn">Send Reset Link</button>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="backToLogin">Back to Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
                            <th><a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary addBtn" ><i class="mdi mdi-table-row-plus-after"></i></a></th>      
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
                                <th><a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary addFertilizationBtn" ><i class="mdi mdi-table-row-plus-after"></i></a></th>
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
                </div>
            </div>
        </div>	
    </div>      
</body>

</html>