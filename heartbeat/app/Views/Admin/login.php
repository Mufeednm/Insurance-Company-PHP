<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">
   <head>
      <meta charset="utf-8" />
      <title><?= $_ENV['APPLICATION_NAME']; ?> | Login</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta content="<?= $_ENV['APPLICATION_NAME']; ?>" name="description" />
      <meta content="Crisant Technologies (OPC) Private Limited" name="author" />
      <!-- App favicon -->
      <link rel="shortcut icon" href="<?= site_url($_ENV['FAVICON']); ?>">
      <script src="<?= site_url(); ?>assets/js/layout.js"></script>
      <link href="<?= site_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
      <link href="<?= site_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
      <link href="<?= site_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" />
      <link href="<?= site_url(); ?>assets/css/custom.min.css" rel="stylesheet" type="text/css" />
      <link href="<?= site_url(); ?>assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
   </head>
   <body style="background-color: #0a3d62;">
      <div class="auth-page-wrapper pt-5">
         <!-- auth page content -->
         <div class="auth-page-content">
            <div class="container">
               <div class="row">
                  <div class="col-lg-12">
                     <div class="text-center mt-sm-5 mb-4 text-white-50">
                        <div>
                           <a href="<?= site_url('login'); ?>" class="d-inline-block auth-logo">
                           <img src="<?= site_url($_ENV['LOGO_LIGHT']); ?>" alt="<?= $_ENV['APPLICATION_NAME']; ?>" height="60">
                           </a>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row justify-content-center">
                  <div class="col-md-8 col-lg-6 col-xl-5">
                     <div class="card mt-4 card-bg-fill">
                        <div class="card-body p-4">
                           <div class="text-center mt-2">
                              <h5 class="text-primary">Welcome Back !</h5>
                              <p class="text-muted">Sign in to continue to <?= $_ENV['APPLICATION_NAME']; ?>.</p>
                           </div>
                           <div class="p-2 mt-4">
                              <?= form_open("admin/login/login", ['class' => 'needs-validation', 'novalidate' => 'novalidate']) ?>
                                 <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" name="userName" id="userName" placeholder="Enter username" required>
                                    <div class="invalid-feedback"> Enter a valid Username</div>
                                 </div>
                                 <div class="mb-3">
                                    <div class="float-end">
                                       <a href="#" class="text-muted">Forgot password?</a>
                                    </div>
                                    <label class="form-label" for="password-input">Password</label>
                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                       <input type="password" class="form-control pe-5 password-input" placeholder="Enter password" name="password" id="password-input" required>
                                       <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                       <div class="invalid-feedback"> Enter a valid Password</div>
                                    </div>
                                 </div>
                                 <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                                    <label class="form-check-label" for="auth-remember-check">Remember me</label>
                                 </div>
                                 <div class="mt-4">
                                    <button class="btn btn-success w-100" type="submit">Sign In</button>
                                 </div>
                              </form>
                           </div>
                        </div>
                        <!-- end card body -->
                     </div>
                     <!-- end card -->
                     <div class="mt-4 text-center">
                        <p class="mb-0 text-white">Don't have an account ? <a href="#" class="fw-semibold text-primary text-decoration-underline"> Signup </a> </p>
                     </div>
                  </div>
               </div>
               <!-- end row -->
            </div>
            <!-- end container -->
         </div>
         <!-- end auth page content -->
         <!-- footer -->
         <footer class="footer">
            <div class="container">
               <div class="row">
                  <div class="row">
                     <div class="col-sm-6 text-white">
                        <script>
                           document.write(new Date().getFullYear())
                        </script> <?= $_ENV['COPYRIGHT']; ?>
                     </div>
                     <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block text-white">
                           Designed & Developed with <i class="ri-heart-fill text-danger"></i> by <a href="https://crisant.com" target="_blank" style="color: #ffffff"><u>Crisant</u></a>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </footer>
         <!-- end Footer -->
      </div>
      <!-- end auth-page-wrapper -->
      <!-- JAVASCRIPT -->
      <script src="<?= site_url(); ?>assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
      <script src="<?= site_url(); ?>assets/libs/simplebar/simplebar.min.js"></script>
      <script src="<?= site_url(); ?>assets/libs/node-waves/waves.min.js"></script>
      <script src="<?= site_url(); ?>assets/libs/feather-icons/feather.min.js"></script>
      <script src="<?= site_url(); ?>assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
      <script src="<?= site_url(); ?>assets/js/plugins.js"></script>
      <script src="<?= site_url(); ?>assets/js/pages/form-validation.init.js"></script>
      <script src="<?= site_url(); ?>assets/js/pages/password-addon.init.js"></script>
      <script src="<?= site_url(); ?>assets/libs/sweetalert2/sweetalert2.min.js"></script>
      <script src="<?= site_url(); ?>assets/js/pages/sweetalerts.init.js"></script>
      <?php if (session()->has('success')) : ?>
      <script>
         Swal.fire({
            title: 'Good job!',
            text: "<?= session('success') ?>",
            icon: "success",
            padding: '2em'
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
   </body>
</html>