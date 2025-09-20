<!-- app/Views/Admin/Report/index.php -->
<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?><?= $const["items"]; ?><?= $this->endSection() ?>

<?= $this->section("headercss") ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<style>
/* small adjustments to keep parity with other pages */
.card { border-radius: 8px; }
.table.table-sm { font-size: 0.9rem; }
.table.table-bordered th, .table.table-bordered td { vertical-align: middle; }
.badge-rounded { border-radius: 999px; padding: 6px 10px; font-weight: 600; font-size: 12px; }

/* Table spacing */
#reportTable tbody tr td { padding: 12px 10px; font-size: 0.95rem; }
#reportTable thead th { padding: 14px 10px; font-size: 0.95rem; }

/* Keep accordion header compact */
#filterAccordion .accordion-button { padding: 0.6rem 1rem; }
.filter-toggle-btn { min-width: 140px; }

/* When filter collapsed show small info line */
.filter-collapsed-info { font-size: 0.9rem; color: #6c757d; }
</style>
<?= $this->endSection() ?>

<?= $this->section("content") ?>

<?php
// helper to safely pull values
if (! function_exists('getv')) {
    function getv($item, $key, $default = '') {
        if (is_array($item)) return $item[$key] ?? $default;
        if (is_object($item)) {
            if (isset($item->$key)) return $item->$key;
            if (method_exists($item, 'toArray')) {
                $arr = $item->toArray();
                return $arr[$key] ?? $default;
            }
            $camel = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $key)));
            $getMethod = 'get' . $camel;
            if (method_exists($item, $getMethod)) {
                try { return $item->{$getMethod}(); } catch (\Exception $e) {}
            }
            try { return $item->$key ?? $default; } catch (\Exception $e) { return $default; }
        }
        return $default;
    }
}
$hasResults = !empty($data['table']);
?>

<!-- page header -->
<div class="row">
   <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
         <h4 class="mb-sm-0"><?= $const["items"]; ?></h4>
         <div class="page-title-right">
            <ol class="breadcrumb m-0">
               <li class="breadcrumb-item"><a href="<?= site_url('dashboard'); ?>">Dashboard</a></li>
               <li class="breadcrumb-item"><a href="javascript:void(0);">Report</a></li>
               <li class="breadcrumb-item active"><?= $const["items"]; ?></li>
            </ol>
         </div>
      </div>
   </div>
</div>

<div class="row">
   <div class="col-lg-12">

      <!-- Accordion: FILTER -->
      <div class="accordion mb-3" id="filterAccordion">
         <div class="accordion-item">
            <?php
               // If no results -> show filter open. If results -> collapse the filter so results area is primary.
               $accordionShowClass = $hasResults ? '' : 'show';
               $ariaExpanded = $hasResults ? 'false' : 'true';
            ?>
            <h2 class="accordion-header" id="filterHeading">
               <button class="accordion-button <?= $hasResults ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="<?= $ariaExpanded ?>" aria-controls="filterCollapse">
                  Filter Report
               </button>
            </h2>

            <div id="filterCollapse" class="accordion-collapse collapse <?= $accordionShowClass ?>" aria-labelledby="filterHeading" data-bs-parent="#filterAccordion">
               <div class="accordion-body">
                  <?= form_open(ROUTE, ['method'=>'get','id'=>'reportForm','class'=>'row g-3 align-items-end']); ?>
                      <div class="col-md-4">
                          <label class="form-label">Date Range</label>
                          <input type="text" id="reportRange" name="reportRange" class="form-control" placeholder="<?= date('Y-m-d') . ' to ' . date('Y-m-d'); ?>" value="<?= esc($data['post']['reportRange'] ?? '') ?>" autocomplete="off" />
                      </div>

                      <div class="col-md-4">
                          <label class="form-label">Product</label>
                          <select name="productId" id="productId" class="form-select">
                              <option value="">All Products</option>
                              <?php foreach ($data['products'] as $p): ?>
                                  <option value="<?= esc(getv($p,'productId')) ?>" <?= (!empty($data['post']['productId']) && (string)$data['post']['productId'] === (string)getv($p,'productId')) ? 'selected' : '' ?>>
                                      <?= esc(getv($p,'name')) ?>
                                  </option>
                              <?php endforeach; ?>
                          </select>
                      </div>

                      <div class="col-md-4 text-end">
                          <button type="submit" class="btn btn-primary filter-toggle-btn">Generate</button>
                          <a href="<?= site_url(ROUTE) ?>" class="btn btn-outline-secondary ms-2">Reset</a>
                      </div>
                  </form>
               </div>
            </div>
         </div>
      </div>

      <!-- When results exist show a small "Filters" quick-open control above results -->
      <?php if ($hasResults): ?>
         <div class="mb-2 d-flex justify-content-between align-items-center">
            <div class="filter-collapsed-info">Showing results for: <strong><?= esc($data['post']['reportRange'] ?? 'All dates') ?></strong> — Product: <strong><?= esc((function() use ($data){ foreach($data['products'] as $p){ if(!empty($data['post']['productId']) && (string)$data['post']['productId'] === (string)getv($p,'productId')) return getv($p,'name'); } return 'All'; })()) ?></strong></div>
            <div>
               <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">Edit Filters</button>
            </div>
         </div>
      <?php endif; ?>

      <!-- RESULTS TABLE (only rendered when data present) -->
      <?php if ($hasResults): ?>
         <div class="card shadow-sm">
            <div class="card-body p-3">
               <div class="table-responsive">
                  <table id="reportTable" class="table table-bordered table-striped align-middle table-sm nowrap" style="width:100%">
                     <thead class="thead-dark">
                        <tr>
                           <th>SL#</th>
                           <th>Policy Number</th>
                           <th>Customer</th>
                           <th>Phone</th>
                           <th>Status</th>
                           <th>Product</th>
                           <th>Created</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach ($data['table'] as $i => $row): ?>
                        <tr>
                           <td><?= esc($i+1) ?></td>
                           <td><?= esc($row['policyNumber']) ?></td>
                           <td><?= esc($row['customerName']) ?></td>
                           <td><?= esc($row['customerphone']) ?></td>
                           <td class="text-nowrap">
                              <?php $st = strtolower($row['status'] ?? ''); ?>
                              <?php if ($st === 'active'): ?>
                                 <span class="badge bg-success badge-rounded">Active</span>
                              <?php elseif ($st === 'lapsed' || $st === 'expired'): ?>
                                 <span class="badge bg-warning text-dark badge-rounded"><?= esc(ucfirst($st)) ?></span>
                              <?php elseif ($st === 'cancelled'): ?>
                                 <span class="badge bg-danger badge-rounded">Cancelled</span>
                              <?php else: ?>
                                 <span class="badge bg-secondary badge-rounded"><?= esc(ucfirst($st ?: 'Unknown')) ?></span>
                              <?php endif; ?>
                           </td>
                           <td><?= esc($row['productName']) ?></td>
                           <td><?= esc(!empty($row['created_at']) ? date('d/M/Y H:i', strtotime($row['created_at'])) : '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      <?php endif; ?>

   </div>
</div>

<?= $this->endSection() ?>

<?= $this->section("footerjs") ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // flatpickr date range (same input used for both initial and accordion form)
    if (typeof flatpickr !== 'undefined') {
        flatpickr('#reportRange', { mode:'range', dateFormat:'Y-m-d', allowInput:true });
    }

    // init datatable only if table exists (results present)
    if (document.getElementById('reportTable')) {
        $('#reportTable').DataTable({
            processing: false,
            serverSide: false,
            stateSave: true,
            scrollX: true,
            responsive: true,
            stripeClasses: [],
            language: { searchPlaceholder: "Search..." },
            dom: '<"row"<"col-md-6"B><"col-md-6"f>>rtip',
            buttons: [
                { extend: 'excelHtml5', text: 'Export Excel', className: 'btn btn-sm btn-outline-success' },
                { extend: 'print', text: 'Print', className: 'btn btn-sm btn-outline-secondary' }
            ],
            order: []
        });
    }

    // optional: when user opens the accordion, focus the date input for quicker editing
    var filterCollapseEl = document.getElementById('filterCollapse');
    if (filterCollapseEl) {
        filterCollapseEl.addEventListener('shown.bs.collapse', function () {
            var input = document.getElementById('reportRange');
            if (input) input.focus();
        });
    }
});
</script>
<?= $this->endSection() ?>
