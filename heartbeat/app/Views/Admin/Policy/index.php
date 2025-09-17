<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?><?= $const["items"]; ?><?= $this->endSection() ?>
<?= $this->section("headercss") ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<?= $this->endSection() ?>
<?= $this->section("headerjs") ?><?= $this->endSection() ?>
<?= $this->section("content") ?>

<?php $productOptions = $productOptions ?? []; ?>

<!-- start page title -->
<div class="row">
   <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
         <h4 class="mb-sm-0"><?= $const["items"]; ?></h4>
         <div class="page-title-right">
            <ol class="breadcrumb m-0">
               <li class="breadcrumb-item"><a href="<?= site_url('dashboard'); ?>">Dashboard</a></li>
               <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
               <li class="breadcrumb-item active"><?= $const["items"]; ?></li>
            </ol>
         </div>
      </div>
   </div>
</div>
<!-- end page title -->

<div class="row">
   <div class="col-lg-12">
      <div class="card">
         <div class="card-header border-0">
            <div class="row align-items-center gy-3">
               <div class="col-sm">
                  <!-- Accordion Flush Example (filter toggle inside header) -->
                  <div class="accordion accordion-flush" id="accordionFlushExample">
                     <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                           <button class="accordion-button collapsed"
                                   style="padding:0; background:transparent; box-shadow:none; color:inherit;"
                                   type="button"
                                   data-bs-toggle="collapse"
                                   data-bs-target="#flush-collapseOne"
                                   aria-expanded="false"
                                   aria-controls="flush-collapseOne">
                              <i class="ri-filter-3-line"></i> &nbsp; Filter data
                           </button>
                        </h2>

                        <div id="flush-collapseOne"
                             class="accordion-collapse collapse"
                             aria-labelledby="flush-headingOne"
                             data-bs-parent="#accordionFlushExample">
                           <div class="accordion-body py-2">
                              <div class="row g-2 align-items-center">
                                 <div class="col-auto">
                                    <select id="filterProductId" class="form-select form-select-sm">
                                       <option value="">All Products</option>
                                       <?php foreach ($productOptions as $pid => $pname): ?>
                                          <option value="<?= esc($pid); ?>"><?= esc(ucfirst(strtolower($pname))); ?></option>
                                       <?php endforeach; ?>
                                    </select>
                                 </div>
                                 <!-- Buttons removed as requested -->
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="col-sm-auto">
                  <div class="d-flex gap-1 flex-wrap">
                     <a class="btn btn-secondary add-btn btn-sm" href="<?= site_url($const["route"] . '/new'); ?>">
                        <i class="ri-add-line align-bottom me-1"></i> New <?= $const["item"]; ?>
                     </a>
                  </div>
               </div>
            </div>
         </div>

         <div class="card-body border border-dashed border-end-0 p-0">
            <div class="p-3">
               <table id="dataTable" class="table table-bordered table-striped align-middle table-sm" style="width:100%">
                  <thead class="thead-dark">
                     <tr>
                        <th>SL#</th>
                        <th>PN</th>
                        <th>Product</th>
                        <th>Customer Name</th>
                        <th>Customer Phone</th>
                        <th>Status</th>
                        <th>Modified</th>
                        <th width="100">Action</th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
      </div>
   </div>
   <!--end col-->
</div>

<?= $this->endSection() ?>
<?= $this->section("footerjs") ?>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script>
   var basePath = "<?= site_url(); ?>";

   $(function() {
       if ($("#dataTable").length > 0) {

           c2 = $("#dataTable").DataTable({
               processing: true,
               serverSide: true,
               stateSave: true,
               scrollX: true,
               language: {
                   searchPlaceholder: "Search..."
               },
               ajax: {
                  url: basePath + "<?= $const["route"]; ?>/load",
                  data: function (d) {
                      d.filterProductId = $('#filterProductId').val();
                  }
               },
               stripeClasses: [],
               columnDefs: [{
                   targets: -1,
                   orderable: false,
                   class: "text-center"
               }]
           });

           // auto-apply filter when selection changes
           $('#filterProductId').on('change', function() {
               c2.ajax.reload();
           });

           // (optional) close accordion after selecting — uncomment if desired:
           // $('#filterProductId').on('change', function() {
           //     c2.ajax.reload();
           //     var collapseEl = document.getElementById('flush-collapseOne');
           //     var bsCollapse = bootstrap.Collapse.getInstance(collapseEl) || new bootstrap.Collapse(collapseEl);
           //     bsCollapse.hide();
           // });
       }
   });
</script>
<?= $this->endSection() ?>
