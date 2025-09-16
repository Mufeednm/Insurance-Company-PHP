<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?>Edit <?= $const['item'] ?><?= $this->endSection() ?>
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
                    <li class="breadcrumb-item"><a href="<?= site_url($const['route']); ?>"><?= $const["items"]; ?></a></li>
                    <li class="breadcrumb-item active">Edit <?= $const["item"]; ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <?php $action = $const['route'] . '/update/' . $const['id']; ?>
    <?= form_open($action, ['class'=>'needs-validation','novalidate'=>'novalidate']); ?>
      <div class="card">
        <div class="card-header border-0">
          <h4 class="card-title mb-0">Edit <?= $const['item']; ?></h4>
        </div>

        <div class="card-body border border-dashed">
          <?= $this->include($const['viewfolder'] . "form"); ?>

          <div id="attributes-wrapper" class="mt-4">
            <?php if (!empty($attributes)) {
                // controller should pass $attributes and $attributeValues
                echo view($const['viewfolder'] . '_attributes', ['attributes'=>$attributes, 'values'=>$attributeValues ?? []]);
            } ?>
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
          <button type="submit" class="btn btn-primary btn-label rounded-pill"><i class="ri-edit-2-line label-icon me-1"></i> Update</button>
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

    if (!productSelect) return;

    // Helper to fetch attributes HTML and insert
    function fetchAttributes(pid) {
        if (!pid) {
            attributesWrapper.innerHTML = "";
            return;
        }
        fetch(basePath + "<?= $const['route']; ?>/attributes/" + pid, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => attributesWrapper.innerHTML = html)
            .catch(err => {
                console.error("Failed to load attributes", err);
                attributesWrapper.innerHTML = '<div class="alert alert-danger">Failed to load attributes</div>';
            });
    }

    // Only fetch when the user *changes* the product selection
    productSelect.addEventListener("change", function () {
        const pid = this.value;
        fetchAttributes(pid);
    });

    // If the server already rendered attributes (edit flow), do NOT overwrite them.
    // Only fetch on page load when the wrapper is empty (this happens in 'new' flow).
    var wrapperHasContent = attributesWrapper && attributesWrapper.innerHTML.trim().length > 0;
    if (!wrapperHasContent && productSelect.value) {
        // new() page: fetch attributes for the selected product
        fetchAttributes(productSelect.value);
    }
});
</script>

<?= $this->endSection() ?>
