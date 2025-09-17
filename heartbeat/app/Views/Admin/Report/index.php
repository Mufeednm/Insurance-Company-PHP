<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?><?= $const["items"]; ?><?= $this->endSection() ?>

<?= $this->section("headercss") ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* Stage 1 - large centered filter card */
.filter-box {
    background: #fff;
    border-radius: 10px;
    padding: 28px;
    box-shadow: 0 8px 30px rgba(20,20,20,0.06);
    margin-bottom: 30px;
}
.filter-box h4 { margin-bottom: 18px; font-weight: 600; }

/* Stage 2 - compact filter bar above results */
.filter-bar {
    background: #ffffff;
    border-radius: 8px;
    padding: 10px 14px;
    box-shadow: 0 4px 18px rgba(20,20,20,0.04);
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.filter-bar .form-control { min-width: 200px; }
.filter-bar .btn { white-space: nowrap; }

/* small responsive tweaks */
@media (max-width: 768px) {
    .filter-bar { padding: 10px; gap: 8px; }
    .filter-box { padding: 18px; }
}
.dataTables_wrapper .dt-buttons .btn { margin-right: 6px; }
.dataTables_wrapper .dataTables_filter input { border-radius: 6px; padding: 6px 10px; }
</style>
<?= $this->endSection() ?>

<?php
// safe getter for arrays, objects, entities
if (! function_exists('getv')) {
    function getv($item, $key, $default = '') {
        if (is_array($item)) {
            return $item[$key] ?? $default;
        }
        if (is_object($item)) {
            if (isset($item->$key)) return $item->$key;
            if (method_exists($item, 'toArray')) {
                $arr = $item->toArray();
                return $arr[$key] ?? $default;
            }
            // try getter
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

<?= $this->section("content") ?>
<div id="content" class="main-content">
    <div class="layout-px-spacing mt-4">

        <div class="page-header">
            <div class="page-title">
                <h3><?= esc($const['items'] ?? 'Report') ?></h3>
            </div>
            <nav class="breadcrumb-one" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= site_url(); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Reports</a></li>
                    <li class="breadcrumb-item active"><a href="<?= site_url('admin/reports'); ?>"><?= esc($const['items'] ?? 'Report') ?></a></li>
                </ol>
            </nav>
        </div>

        <!-- ==== Stage 1: Large centered form (only when NO results) ==== -->
        <?php if (!$hasResults): ?>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="filter-box">
                    <h4>Select Report Filters</h4>
                    <?php $attributes = ['name' => 'report', 'id' => 'report', 'method' => 'get']; ?>
                    <?= form_open(ROUTE, $attributes); ?>
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="reportRange" class="form-label">Date Range</label>
                            <input id="reportRange" name="reportRange" type="text" class="form-control" placeholder="YYYY-MM-DD to YYYY-MM-DD" value="<?= esc($data['post']['reportRange'] ?? '') ?>" autocomplete="off" />
                        </div>
                        <div class="col-md-6">
                            <label for="productId" class="form-label">Product</label>
                            <select class="form-control select2" id="productId" name="productId">
                                <option value="">All products</option>
                                <?php if (!empty($data['products'])): ?>
                                    <?php foreach ($data['products'] as $p):
                                        $pid   = getv($p, 'productId');
                                        $pname = getv($p, 'name');
                                        $selected = (isset($data['post']['productId']) && (string)$data['post']['productId'] === (string)$pid) ? "selected" : "";
                                    ?>
                                        <option value="<?= esc($pid) ?>" <?= $selected; ?>><?= esc($pname) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                        <a href="<?= site_url(ROUTE) ?>" class="btn btn-outline-secondary">Reset</a>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ==== Stage 2: Compact filter bar (only when results exist) ==== -->
        <?php if ($hasResults): 
            // compute friendly product name for the summary
            $selectedProductId = $data['post']['productId'] ?? '';
            $selectedProductName = '';
            if (!empty($selectedProductId) && !empty($data['products'])) {
                foreach ($data['products'] as $pp) {
                    if ((string)getv($pp, 'productId') === (string)$selectedProductId) { $selectedProductName = getv($pp, 'name'); break; }
                }
            }
            $selectedRange = $data['post']['reportRange'] ?? '';
        ?>
        <div class="row mb-2">
            <div class="col-lg-12">
                <div class="filter-bar">
                    <div style="min-width:180px;">
                        <strong>Filters:</strong>
                    </div>
                    <div>
                        <small class="text-muted">Date:</small><br>
                        <div><?= esc($selectedRange ?: 'All dates') ?></div>
                    </div>
                    <div>
                        <small class="text-muted">Product:</small><br>
                        <div><?= esc($selectedProductName ?: 'All products') ?></div>
                    </div>

                    <div style="margin-left:auto; display:flex; gap:8px; align-items:center;">
                        <!-- inline small form to quickly change filters -->
                        <?php $attributes = ['name' => 'reportCompact', 'id' => 'reportCompact', 'method' => 'get', 'style' => 'display:flex;gap:8px;align-items:center;'] ; ?>
                        <?= form_open(ROUTE, $attributes); ?>
                            <input id="reportRangeCompact" name="reportRange" type="text" class="form-control" placeholder="YYYY-MM-DD to YYYY-MM-DD" value="<?= esc($data['post']['reportRange'] ?? '') ?>" autocomplete="off" style="min-width:200px;height:36px;padding:6px 10px;" />
                            <select class="form-control select2" id="productIdCompact" name="productId" style="min-width:180px;height:36px;">
                                <option value="">All products</option>
                                <?php if (!empty($data['products'])): ?>
                                    <?php foreach ($data['products'] as $p):
                                        $pidc   = getv($p, 'productId');
                                        $pnamec = getv($p, 'name');
                                        $selectedc = (isset($data['post']['productId']) && (string)$data['post']['productId'] === (string)$pidc) ? "selected" : "";
                                    ?>
                                        <option value="<?= esc($pidc) ?>" <?= $selectedc; ?>><?= esc($pnamec) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                            <a href="<?= site_url(ROUTE) ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ==== Results table (if any) ==== -->
        <?php if ($hasResults): ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="widget-content widget-content-area">
                    <div class="table-responsive">
                        <table id="reportTable" class="table table-striped align-middle" style="width:100%">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Policy Number</th>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Product</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['table'] as $row): ?>
                                    <tr>
                                        <td><?= esc(getv($row, 'policyId')) ?></td>
                                        <td><?= esc(getv($row, 'policyNumber')) ?></td>
                                        <td><?= esc(getv($row, 'customerName')) ?></td>
                                        <td><?= esc(getv($row, 'customerphone')) ?></td>
                                        <td><?= esc(getv($row, 'status')) ?></td>
                                        <td><?= esc(getv($row, 'productName')) ?></td>
                                        <td><?= esc(getv($row, 'created_at')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // init flatpickr - primary form or compact form
    if (typeof flatpickr !== 'undefined') {
        if (document.getElementById('reportRange')) {
            flatpickr('#reportRange', { mode: 'range', dateFormat: 'Y-m-d', allowInput: true });
        }
        if (document.getElementById('reportRangeCompact')) {
            flatpickr('#reportRangeCompact', { mode: 'range', dateFormat: 'Y-m-d', allowInput: true });
        }
    }

    // init select2 for present selects
    if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
        $('#productId').select2({ width: '100%' });
        $('#productIdCompact').select2({ width: '100%' });
    }

    // DataTable init when results present
    if (document.getElementById('reportTable') && typeof $ !== 'undefined' && $.fn && $.fn.DataTable) {
        $('#reportTable').DataTable({
            dom: '<"row"<"col-md-6"B><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            buttons: [
                { extend: 'excel', className: 'btn btn-sm btn-outline-success' },
                { extend: 'print', className: 'btn btn-sm btn-outline-secondary' }
            ],
            pageLength: 25,
            responsive: true,
            order: [],
            language: {
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_ entries"
            }
        });
    }

    // Optional: show a brief loading UI when submitting
    $('#report, #reportCompact').on('submit', function () {
        if (typeof $.blockUI !== 'undefined') {
            $.blockUI({ message: 'Generating report…', overlayCSS: { backgroundColor: '#000', opacity: 0.35 }, css: { color: '#fff' }});
            setTimeout($.unblockUI, 1500);
        }
    });
});
</script>
<?= $this->endSection() ?>
