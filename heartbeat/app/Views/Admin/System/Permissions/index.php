<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?>Dashboard<?= $this->endSection() ?>
<?= $this->section("headercss") ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<?= $this->endSection() ?>
<?= $this->section("headerjs") ?><?= $this->endSection() ?>
<?= $this->section("content") ?>
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
                  <!-- Accordion Flush Example -->
               </div>
               <div class="col-sm-auto">
                  <div class="d-flex gap-1 flex-wrap">
                     <a class="btn btn-secondary add-btn btn-sm" href="<?= site_url($const["route"] . '/new'); ?>"><i class="ri-add-line align-bottom me-1"></i> New <?= $const["item"]; ?></a>
                  </div>
               </div>
            </div>
         </div>
         <div class="card-body border border-dashed border-end-0">
            <table id="dataTable" class="table table-bordered table-striped align-middle table-sm" style="width:100%">
               <thead class="thead-dark">
                  <tr>
                    <th width="80">SL #</th>
                    <th>Description</th>
                    <th>Permission Key</th>
                    <th>Group</th>
                    <th>Created</th>
                    <th>Modified</th>
                    <th>Action</th>
                </tr>
               </thead>
            </table>
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
           $("#dataTable").DataTable({
               "processing": true,
               "serverSide": true,
               "stateSave": true,
               "scrollX": true,
               "language": {
                   searchPlaceholder: "Search..."
               },
               "ajax": basePath + "<?= $const["route"]; ?>/load",
               "stripeClasses": [],
               columnDefs: [
                  {
                     targets: -1,
                     orderable: false,
                     className: "text-center"
                  }
               ]
           });
       }
   });
</script>
<?= $this->endSection() ?>