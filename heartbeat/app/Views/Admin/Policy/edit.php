<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?>Edit <?= esc($const['item']) ?><?= $this->endSection() ?>
<?= $this->section("content") ?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0"><?= esc($const["items"]) ?></h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="<?= site_url('dashboard'); ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= site_url($const['route']); ?>"><?= esc($const["items"]) ?></a></li>
          <li class="breadcrumb-item active">Edit <?= esc($const["item"]) ?></li>
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
          <h4 class="card-title mb-0">Edit <?= esc($const['item']) ?></h4>
        </div>

        <div class="card-body border border-dashed">
          <?= $this->include(rtrim($const['viewfolder'], '/') . '/form'); ?>

          <!-- render attributes HTML server-side into a JS-safe variable, but DO NOT output here directly.
               We'll insert it into the attributes placeholder so it becomes sibling cols (like New). -->
          <?php if (!empty($attributes)): 
              // render attributes to a string (they are blocks with .attr-col)
              $renderedAttributes = view($const['viewfolder'] . '_attributes', ['attributes'=>$attributes, 'values'=>$attributeValues ?? []]);
          else:
              $renderedAttributes = '';
          endif; ?>
          <div id="serverRenderedAttributes" style="display:none"><?= htmlspecialchars($renderedAttributes, ENT_QUOTES, 'UTF-8') ?></div>

          <?php if (session()->has('error')) : ?>
            <div class="row mt-3">
              <div class="col">
                <div class="alert alert-danger" role="alert">
                  <ul style="margin-bottom:0px;">
                    <?php foreach ((array)session('error') as $error) : ?>
                      <li><?= esc($error) ?></li>
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
    <?= form_close(); ?>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section("footerjs") ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    var basePath = "<?= site_url(); ?>";
    const productSelect = document.querySelector('select[name="productId"]');
    const attributesRow = document.getElementById("attributes-row");
    const placeholder = document.getElementById("attributes-placeholder");

    // Inject server-rendered attributes (if any) into the placeholder inside attributes-row
    const serverHtmlHolder = document.getElementById('serverRenderedAttributes');
    if (serverHtmlHolder && serverHtmlHolder.textContent.trim().length > 0) {
        // decode the HTML string we safely encoded server-side
        const html = serverHtmlHolder.textContent;
        // remove placeholder and insert attribute columns
        if (placeholder && attributesRow.contains(placeholder)) placeholder.remove();
        attributesRow.insertAdjacentHTML('beforeend', html);
    }

    function clearAttributeCols() {
        const old = attributesRow.querySelectorAll('.attr-col');
        old.forEach(n => n.remove());
    }

    function fetchAndInsertColumns(pid) {
        if (!pid) {
            if (placeholder && !attributesRow.contains(placeholder)) {
                attributesRow.appendChild(placeholder);
            }
            return;
        }

        // remove placeholder
        if (placeholder && attributesRow.contains(placeholder)) {
            placeholder.remove();
        }

        fetch(basePath + "<?= $const['route']; ?>/attributes/" + pid, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => {
                if (!r.ok) throw new Error('Network response was not ok');
                return r.text();
            })
            .then(html => {
                attributesRow.insertAdjacentHTML('beforeend', html);
            })
            .catch(err => {
                console.error("Failed to load attributes", err);
                attributesRow.insertAdjacentHTML('beforeend', '<div class="col-12 attr-col"><div class="alert alert-danger">Failed to load attributes</div></div>');
            });
    }

    if (!productSelect || !attributesRow) return;

    productSelect.addEventListener("change", function () {
        clearAttributeCols();
        const pid = this.value;
        fetchAndInsertColumns(pid);
    });

    // if there are no attribute cols rendered yet, fetch them
    const hasCols = attributesRow.querySelectorAll('.attr-col').length > 0;
    if (!hasCols && productSelect.value) {
        fetchAndInsertColumns(productSelect.value);
    }
});
</script>
<?= $this->endSection() ?>
