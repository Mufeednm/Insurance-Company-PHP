<!doctype html>
<html lang="en" data-layout="horizontal" data-topbar="dark" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-bs-theme="light" data-layout-width="fluid" data-layout-position="fixed" data-layout-style="default" data-sidebar-visibility="hide">
<head>
   <meta charset="utf-8" />
   <title><?= $_ENV['APPLICATION_NAME']; ?> | <?= $this->renderSection("title") ?></title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta content="<?= $_ENV['APPLICATION_NAME']; ?>" name="description" />
   <meta content="Crisant Technologies (OPC) Private Limited" name="author" />
   <link rel="shortcut icon" href="<?= site_url($_ENV['FAVICON']); ?>">
   <?= $this->renderSection("headercss") ?>
   <link href="<?= site_url(); ?>assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />
   <link href="<?= site_url(); ?>assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />
   <script src="<?= site_url(); ?>assets/js/layout.js"></script>
   <link href="<?= site_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
   <link href="<?= site_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
   <link href="<?= site_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" />
   <link href="<?= site_url(); ?>assets/css/custom.css" rel="stylesheet" type="text/css" />
   <link href="<?= site_url(); ?>assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
   <link href="<?= site_url(); ?>assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
   <?= $this->renderSection("headerjs") ?>
</head>

<body>
   <!-- Begin page -->
   <div id="layout-wrapper">
      <header id="page-topbar">
         <div class="layout-width">
            <div class="navbar-header">
               <div class="d-flex">
                  <!-- LOGO -->
                  <div class="navbar-brand-box horizontal-logo">
                  <a href="<?= site_url('dashboard'); ?>" class="logo logo-light">
                        <span class="logo-sm">
                           <img src="<?= site_url($_ENV['LOGO_LIGHT']); ?>" alt="<?= $_ENV['APPLICATION_NAME']; ?>" height="40">
                        </span>
                        <span class="logo-lg">
                           <img src="<?= site_url($_ENV['LOGO_LIGHT']); ?>" alt="<?= $_ENV['APPLICATION_NAME']; ?>" height="40">
                        </span>
                  </a>
               </div>
                  <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                     <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                     </span>
                  </button>
                  <!-- App Search-->
                  <form class="app-search d-none d-md-block">
                     <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Search..." autocomplete="off" id="search-options" value="">
                        <span class="mdi mdi-magnify search-widget-icon"></span>
                        <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none" id="search-close-options"></span>
                     </div>
                  </form>
               </div>
               <div class="d-flex align-items-center">
                  <div class="dropdown d-md-none topbar-head-dropdown header-item">
                     <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-search fs-22"></i>
                     </button>
                     <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                        <form class="p-3">
                           <div class="form-group m-0">
                              <div class="input-group">
                                 <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                 <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                              </div>
                           </div>
                        </form>
                     </div>
                  </div>
                  <div class="ms-1 header-item d-none d-sm-flex">
                     <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" data-toggle="fullscreen">
                        <i class='bx bx-fullscreen fs-22'></i>
                     </button>
                  </div>
                  <div class="ms-1 header-item d-none d-sm-flex">
                     <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                        <i class='bx bx-moon fs-22'></i>
                     </button>
                  </div>
                  <div class="dropdown ms-sm-3 header-item topbar-user">
                     <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                           <img class="rounded-circle header-profile-user" src="<?= site_url('assets/images/users/avatar-1.jpg'); ?>" alt="<?= $_ENV['APPLICATION_NAME']; ?>">
                           <span class="text-start ms-xl-2">
                              <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"><?= session()->get('userName'); ?></span>
                              <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">ADMIN</span>
                           </span>
                        </span>
                     </button>
                     <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Welcome <?= session()->get('userName'); ?>!</h6>
                        <a class="dropdown-item" href="#">
                           <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> 
                           <span class="align-middle">Profile</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">
                           <span class="badge bg-success-subtle text-success mt-1 float-end">New</span><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i>
                           <span class="align-middle">Settings</span>
                        </a>
                        <a class="dropdown-item" href="#">
                           <i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Lock screen</span>
                        </a>
                        <a class="dropdown-item" href="<?= site_url('login/logout'); ?>">
                           <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                           <span class="align-middle" data-key="t-logout">Logout</span>
                        </a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </header>
      <!-- ========== App Menu ========== -->
      <div class="app-menu navbar-menu">
         <!-- LOGO -->
         <div id="scrollbar">
            <div class="container-fluid">
               <div id="two-column-menu">
               </div>
               <ul class="navbar-nav" id="navbar-nav">
                  <?php
                    $menuItems = \Config\Menu::items();
                    $permissionModel = new \App\Models\Permissions();
                    $userId = session()->get('loginId');
                    $isAdmin = session()->get('isAdmin') == 1;
                    $currentPage = session()->get('activate');  // your existing logic

                    renderMenu($menuItems, $permissionModel, $userId, $isAdmin, 0, 'topAccordion', $currentPage);
                    ?>
               </ul>
            </div>
            <!-- Sidebar -->
         </div>
         <div class="sidebar-background"></div>
      </div>
      <!-- Left Sidebar End -->
      <!-- Vertical Overlay-->
      <div class="vertical-overlay"></div>
      <!-- ============================================================== -->
      <!-- Start right Content here -->
      <!-- ============================================================== -->
      <div class="main-content">
         <div class="page-content">
            <div class="container-fluid">
               <?= $this->renderSection("content") ?>
            </div>
         </div>
         <!-- End Page-content -->
         <footer class="footer">
            <div class="container-fluid">
               <div class="row">
                  <div class="col-sm-6">
                     <script>
                        document.write(new Date().getFullYear())
                     </script> <?= $_ENV['COPYRIGHT']; ?>
                  </div>
                  <div class="col-sm-6">
                     <div class="text-sm-end d-none d-sm-block">
                        Designed & Developed with <i class="ri-heart-fill text-danger"></i> by <a href="https://crisant.com" target="_blank" style="color: #272a3a">Crisant</a>
                     </div>
                  </div>
               </div>
            </div>
         </footer>
      </div>
      <!-- end main content-->
   </div>
   <!-- END layout-wrapper -->
   <!--start back-to-top-->
   <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
      <i class="ri-arrow-up-line"></i>
   </button>
   <!--end back-to-top-->
   <!-- JAVASCRIPT -->
   <script src="<?= site_url(); ?>assets/libs/jQuery/jquery-3.7.1.min.js"></script>
   <script src="<?= site_url(); ?>assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
   <script src="<?= site_url(); ?>assets/libs/simplebar/simplebar.min.js"></script>
   <script src="<?= site_url(); ?>assets/libs/node-waves/waves.min.js"></script>
   <script src="<?= site_url(); ?>assets/libs/feather-icons/feather.min.js"></script>
   <script src="<?= site_url(); ?>assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
   <script src="<?= site_url(); ?>assets/js/plugins.js"></script>
   <script src="<?= site_url(); ?>assets/libs/sweetalert2/sweetalert2.min.js"></script>
   <script src="<?= site_url(); ?>assets/libs/select2/js/select2.full.min.js"></script>
   <?= $this->renderSection("footerjs") ?>
   <script src="<?= site_url(); ?>assets/js/app.js"></script>
   <script>
      var basePath = "<?= site_url(); ?>";
   </script>
   <!-- Page JS -->
   <?php if (session()->has('success')) : ?>
      <script>
         var t;
         Swal.fire({
            title: "Good job!",
            icon: "success",
            html: "<?= session('success') ?>",
            timer: 1e3,
            timerProgressBar: !0,
            didOpen: function() {
               Swal.showLoading(), t = setInterval(function() {
                  var t, e = Swal.getHtmlContainer();
                  !e || (t = e.querySelector("b")) && (t.textContent = Swal.getTimerLeft())
               }, 100)
            },
            onClose: function() {
               clearInterval(t)
            }
         });
      </script>
   <?php endif; ?>
   <?php if (session()->has('warning')) : ?>
      <script>
         Swal.fire({
            title: 'Oops, Sorry!',
            text: "<?= session('warning') ?>",
            icon: "warning",
            padding: '2em'
         });
      </script>
   <?php endif; ?>
   <?php if (session()->has('danger')) : ?>
      <script>
         Swal.fire({
            title: 'Oops, Sorry!',
            text: "<?= session('danger') ?>",
            icon: "warning",
            padding: '2em'
         });
      </script>
   <?php endif; ?>
   <?php if (session()->has('reload')) : ?>
      <script>
         var t;
         Swal.fire({
            title: "Please wait !",
            icon: "info",
            html: "<?= session('reload') ?>",
            timer: 2e3,
            timerProgressBar: !0,
            didOpen: function() {
               Swal.showLoading(), t = setInterval(function() {
                  var t, e = Swal.getHtmlContainer();
                  !e || (t = e.querySelector("b")) && (t.textContent = Swal.getTimerLeft())
               }, 100)
            },
            onClose: function() {
               clearInterval(t)
            }
         });
      </script>
   <?php endif; ?>
   <!-- Page JS -->
   <script>
      $(".select2").select2();

         $(document).on('shown.bs.dropdown', '.table-responsive .dropdown', function () {
         let menu = $(this).find('.dropdown-menu');
         $('body').append(menu.detach());
         let eOffset = $(this).offset();
         menu.css({
            'display': 'block',
            'top': eOffset.top + $(this).outerHeight(),
            'left': eOffset.left,
            'position': 'absolute'
         });
      });

      $(document).on('hidden.bs.dropdown', '.table-responsive .dropdown', function () {
         let menu = $(this).find('.dropdown-menu');
         $(this).append(menu.detach());
      });
      
   </script>
</body>

</html>