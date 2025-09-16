<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?>Delete <?= $const["item"]; ?><?= $this->endSection() ?>
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
                    <li class="breadcrumb-item active">Delete <?= $const["item"]; ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <?= form_open($const['route'] . '/delete/' . $const['id']); ?>
      <div class="card">
        <div class="card-header border-0">
          <h4 class="card-title mb-0">Delete <?= $const['item']; ?></h4>
        </div>
        <div class="card-body border border-dashed">
          <div class="alert alert-danger" role="alert">
            <strong>Warning!</strong> Are you sure you want to delete <strong><?= $const['identifier']; ?></strong> ?
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
          <button type="submit" class="btn btn-success btn-label rounded-pill"><i class="ri-check-double-line label-icon me-1"></i> Yes, Delete</button>
          <a href="<?= site_url($const['route']); ?>" class="btn btn-danger btn-label rounded-pill"><i class="ri-close-line label-icon me-1"></i> Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>
