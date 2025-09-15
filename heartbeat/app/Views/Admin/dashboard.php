<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?>Dashboard<?= $this->endSection() ?>
<?= $this->section("headercss") ?><?= $this->endSection() ?>
<?= $this->section("headerjs") ?><?= $this->endSection() ?>
<?= $this->section("content") ?>
<div class="row">
   <div class="col-xxl-5">
 
   <div class="d-flex flex-column h-100">
         <div class="row h-100">
            <div class="col-12">
               <div class="card">
                  <div class="card-body p-0">
                     <div class="alert alert-warning border-0 rounded-0 m-0 d-flex align-items-center" role="alert">
                        <i data-feather="alert-triangle" class="text-warning me-2 icon-sm"></i>
                        <div class="flex-grow-1 text-truncate">
                           Your free trial expired in <b>17</b> days.
                        </div>
                        <div class="flex-shrink-0">
                           <a href="pages-pricing.html" class="text-reset text-decoration-underline"><b>Upgrade</b></a>
                        </div>
                     </div>
                     <div class="row align-items-end">
                        <div class="col-sm-8">
                           <div class="p-3">
                              <p class="fs-16 lh-base">Upgrade your plan from a <span class="fw-semibold">Free trial</span>, to 'Premium Plan' <i class="mdi mdi-arrow-right"></i></p>
                              <div class="mt-3">
                                 <a href="pages-pricing.html" class="btn btn-success">Upgrade Account!</a>
                              </div>
                           </div>
                        </div>
                        <div class="col-sm-4">
                           <div class="px-3">
                              <img src="<?= site_url(); ?>assets/images/user-illustarator-2.png" class="img-fluid" alt="">
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- end card-body-->
               </div>
            </div>
            <!-- end col-->
         </div>
         <!-- end row-->
         <div class="row">
            <div class="col-md-6">
               <div class="card card-animate">
                  <div class="card-body">
                     <div class="d-flex justify-content-between">
                        <div>
                           <p class="fw-medium text-muted mb-0">Total Orders</p>
                           <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="28">0</span></h2>
                        </div>
                        <div>
                           <div class="avatar-sm flex-shrink-0">
                              <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                              <i data-feather="users" class="text-info"></i>
                              </span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- end card body -->
               </div>
               <!-- end card-->
            </div>
            <!-- end col-->
            <div class="col-md-6">
               <div class="card card-animate">
                  <div class="card-body">
                     <div class="d-flex justify-content-between">
                        <div>
                           <p class="fw-medium text-muted mb-0">In-Transit</p>
                           <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="20">0</span></h2>
                        </div>
                        <div>
                           <div class="avatar-sm flex-shrink-0">
                              <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                              <i data-feather="users" class="text-info"></i>
                              </span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- end card body -->
               </div>
               <!-- end card-->
            </div>
            <!-- end col-->
         </div>
         <!-- end row-->
         <div class="row">
         <div class="col-md-6">
               <div class="card card-animate">
                  <div class="card-body">
                     <div class="d-flex justify-content-between">
                        <div>
                           <p class="fw-medium text-muted mb-0">Delivered</p>
                           <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="8">0</span></h2>
                        </div>
                        <div>
                           <div class="avatar-sm flex-shrink-0">
                              <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                              <i data-feather="users" class="text-info"></i>
                              </span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- end card body -->
               </div>
               <!-- end card-->
            </div>
            <!-- end col-->
            <div class="col-md-6">
               <div class="card card-animate">
                  <div class="card-body">
                     <div class="d-flex justify-content-between">
                        <div>
                           <p class="fw-medium text-muted mb-0">RTO</p>
                           <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="2">0</span></h2>
                        </div>
                        <div>
                           <div class="avatar-sm flex-shrink-0">
                              <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                              <i data-feather="users" class="text-info"></i>
                              </span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- end card body -->
               </div>
               <!-- end card-->
            </div>
            <!-- end col-->
         </div>
         <!-- end row-->
      </div>
<?= $this->endSection() ?>
<?= $this->section("footerjs") ?>
<script src="<?= site_url(); ?>assets/libs/apexcharts/apexcharts.min.js"></script>
<script src="<?= site_url(); ?>assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
<script src="<?= site_url(); ?>assets/libs/jsvectormap/maps/world-merc.js"></script>
<script src="<?= site_url(); ?>assets/libs/swiper/swiper-bundle.min.js"></script>
<script src="<?= site_url(); ?>assets/js/pages/dashboard-ecommerce.init.js"></script>
<!-- apexcharts -->
<script src="<?= site_url(); ?>assets/libs/apexcharts/apexcharts.min.js"></script>
<script src="<?= site_url(); ?>assets/libs/swiper/swiper-bundle.min.js"></script>
<script src="<?= site_url(); ?>assets/js/pages/dashboard-crypto.init.js"></script>
<script src="<?= site_url(); ?>assets/libs/sweetalert2/sweetalert2.min.js"></script>
<!-- Page JS -->
<?php if (session()->has('success')) : ?>
      <script>
         var t;
         Swal.fire({
            title: "Good job!",
            icon: "success",
            html: "<?= session('success') ?>",
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
<?= $this->endSection() ?>
