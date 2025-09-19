<!-- app/Views/Admin/Report/index.php -->
<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?><?= $const["items"]; ?><?= $this->endSection() ?>

<?= $this->section("headercss") ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.card { border-radius: 8px; }
.card .card-header { padding: 12px 16px; }
.card .card-body { padding: 0; }
.table.table-sm { font-size: 0.9rem; }
.table.table-bordered th, .table.table-bordered td { vertical-align: middle; }

.filter-box { background:#fff;border-radius:10px;padding:16px;box-shadow:0 8px 24px rgba(10,10,10,0.04); }
.filter-bar { background:#fff; border-radius:8px; padding:10px; box-shadow:0 4px 14px rgba(10,10,10,0.03); margin-bottom:14px; }

.badge-rounded { border-radius:999px; padding:6px 10px; font-weight:600; font-size:12px; }

/* Make report table rows taller and more readable */
#reportTable tbody tr td {
    padding: 12px 10px;   /* more vertical space */
    font-size: 0.95rem;   /* slightly larger font */
}

#reportTable thead th {
    padding: 14px 10px;
    font-size: 0.95rem;
}

</style>
<?= $this->endSection() ?>

<?= $this->section("content") ?>

<?php
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
$today = date('Y-m-d');
$placeholder = $today . ' to ' . $today;
?>

<div class="row">
   <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
         <h4 class="mb-sm-0"><?= $const["items"]; ?></h4>
         <div class="page-title-right">
            <ol class="breadcrumb m-0">
               <li class="breadcrumb-item"><a href="<?= site_url('dashboard'); ?>">Dashboard</a></li>
               <li class="breadcrumb-item"><a href="javascript: void(0);">Report</a></li>
               <li class="breadcrumb-item active"><?= $const["items"]; ?></li>
            </ol>
         </div>
      </div>
   </div>
</div>

<?php $hasResults = !empty($data['table']); ?>
<div class="container-fluid mt-3">

    <!-- Stage 1: Large filter (only when no results yet) -->
    <?php if (!$hasResults): ?>
    <div class="row justify-content-center mb-3">
        <div class="col-lg-8">
            <div class="filter-box">
                <h5 class="mb-3">Generate Report</h5>
                <?= form_open(ROUTE, ['id'=>'report','method'=>'get']); ?>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">Date Range</label>
                        <input id="reportRange" name="reportRange" type="text" 
                               class="form-control" 
                               placeholder="<?= $placeholder ?>" 
                               value="<?= esc($data['post']['reportRange'] ?? '') ?>" 
                               autocomplete="off" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Product</label>
                        <select class="form-control select2" id="productId" name="productId">
                            <option value="">All products</option>
                            <?php foreach ($data['products'] as $p): ?>
                                <option value="<?= esc(getv($p,'productId')) ?>" <?= (isset($data['post']['productId']) && (string)$data['post']['productId'] === (string)getv($p,'productId'))?'selected':''; ?>><?= esc(getv($p,'name')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button class="btn btn-primary">Generate</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Stage 2: Results with inline filter + DataTable -->
    <?php if ($hasResults): ?>
    <div class="row">
       <div class="col-lg-12">
          <div class="card">
             <div class="card-header border-0">
                <h5 class="mb-0">Report Results</h5>
             </div>

             <div class="card-body border border-dashed border-end-0 p-0">
                <div class="p-3">

                   <!-- Inline filter bar -->
                   <div class="filter-bar mb-3">
                       <?= form_open(ROUTE, ['id'=>'reportCompact','method'=>'get','class'=>'row g-2 align-items-end w-100']); ?>

                           <div class="col-md-4 col-sm-6">
                               <label for="reportRangeCompact" class="form-label">Date Range</label>
                               <input id="reportRangeCompact" name="reportRange" type="text" 
                                      class="form-control" 
                                      placeholder="<?= $placeholder ?>" 
                                      value="<?= esc($data['post']['reportRange'] ?? '') ?>" 
                                      autocomplete="off" />
                           </div>

                           <div class="col-md-4 col-sm-6">
                               <label for="productIdCompact" class="form-label mb-1">Product</label>
                               <select class="form-control form-control-sm select2" 
                                       id="productIdCompact" name="productId">
                                   <option value="">All products</option>
                                   <?php foreach ($data['products'] as $p): ?>
                                       <option value="<?= esc(getv($p,'productId')) ?>" <?= (isset($data['post']['productId']) && (string)$data['post']['productId'] === (string)getv($p,'productId'))?'selected':''; ?>><?= esc(getv($p,'name')) ?></option>
                                   <?php endforeach; ?>
                               </select>
                           </div>

                           <div class="col-md-4 col-sm-12 text-start text-md-end">
                               <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                               <a href="<?= site_url(ROUTE) ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
                           </div>

                       </form>
                   </div>

                   <!-- DataTable -->
                   <table id="reportTable" class="table table-bordered table-striped align-middle table-sm" style="width:100%">
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
                             <td>
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
       </div>
    </div>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>

<?= $this->section("footerjs") ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){
    flatpickr('#reportRange', { mode: 'range', dateFormat: 'Y-m-d', allowInput: true });
    flatpickr('#reportRangeCompact', { mode: 'range', dateFormat: 'Y-m-d', allowInput: true });

    $('#productId').select2({ width:'100%' });
    $('#productIdCompact').select2({ width:'100%' });

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

    $('#report, #reportCompact').on('submit', function(){
        $(this).find('button[type=submit]').prop('disabled',true).text('Generating...');
    });
});
</script>
<?= $this->endSection() ?>
