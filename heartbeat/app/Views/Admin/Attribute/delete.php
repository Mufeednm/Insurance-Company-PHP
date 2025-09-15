<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?>Delete <?= $const["item"]; ?><?= $this->endSection() ?>

<?= $this->section("content") ?>
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body text-center">
        <h4 class="card-title">Delete <?= $const["item"]; ?></h4>
        <p>Are you sure you want to delete this <?= strtolower($const["item"]); ?>?</p>

        <form method="post" action="<?= site_url($const["route"]."/delete/".$const["id"]); ?>">
          <?= csrf_field(); ?>
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
          <a href="<?= site_url($const["route"]); ?>" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
