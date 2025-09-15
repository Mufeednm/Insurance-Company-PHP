<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?>New <?= $const["item"]; ?><?= $this->endSection() ?>
<?= $this->section("headercss") ?><?= $this->endSection() ?>
<?= $this->section("headerjs") ?><?= $this->endSection() ?>
<?= $this->section("content") ?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0"><?= $const["items"]; ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard'); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Policies</a></li>
                    <li class="breadcrumb-item active">New <?= $const["item"]; ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <?= form_open(ROUTE . '/create', ['class'=>'needs-validation','novalidate'=>'novalidate']); ?>
      <div class="card">
        <div class="card-header border-0">
          <h4 class="card-title mb-0">New <?= $const['item']; ?></h4>
        </div>

        <div class="card-body border border-dashed">
            <?= $this->include($const['viewfolder'] . "form"); ?>

            <!-- Attributes wrapper inside form so inputs submit -->
            <div id="attributes-wrapper" class="mt-3">
                <?php if (!empty($attributes) && is_array($attributes)): ?>
                    <?= $this->include($const['viewfolder'] . '_attributes'); ?>
                <?php else: ?>
                    <div class="mb-2 text-muted">Select a product to load attributes.</div>
                <?php endif; ?>
            </div>

            <?php if (session()->has('error')) : ?>
              <div class="row mt-3">
                <div class="col">
                  <div class="alert alert-danger" role="alert">
                    <ul style="margin-bottom:0px;">
                      <?php foreach ((array)session('error') as $error) : ?>
                        <li><?= $error ?></li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                </div>
              </div>
            <?php endif ?>
        </div>

        <div class="card-footer border-0 d-flex justify-content-end gap-2">
          <button type="submit" class="btn btn-primary btn-label rounded-pill"><i class="ri-check-double-line label-icon me-1"></i> Save</button>
          <a href="<?= site_url($const['route']); ?>" class="btn btn-danger btn-label rounded-pill"><i class="ri-close-line label-icon me-1"></i> Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section("footerjs") ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    var basePath = "<?= site_url(); ?>";
    const productSelect = document.querySelector('select[name="productId"]');
    const attributesWrapper = document.getElementById("attributes-wrapper");

    if (productSelect) {
        productSelect.addEventListener("change", function () {
            const pid = this.value;
            if (!pid) {
                attributesWrapper.innerHTML = '<div class="mb-2 text-muted">Select a product to load attributes.</div>';
                return;
            }
            fetch(basePath + "<?= $const['route']; ?>/attributes/" + pid, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => attributesWrapper.innerHTML = html)
                .catch(err => {
                    console.error("Failed to load attributes", err);
                    attributesWrapper.innerHTML = '<div class="alert alert-danger">Failed to load attributes</div>';
                });
        });
    }
});
</script>
<?= $this->endSection() ?>
