<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?>Edit <?= $const["item"]; ?><?= $this->endSection() ?>
<?= $this->section("headercss") ?><?= $this->endSection() ?>
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
                    <li class="breadcrumb-item"><a href="<?= site_url($const["route"]); ?>"><?= $const["items"]; ?></a></li>
                    <li class="breadcrumb-item active">Edit <?= $const["item"]; ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->
<!-- end row -->
<div class="row">
    <div class="col-lg-12">
        <?php $attributes = array('class' => 'needs-validation', 'novalidate' => 'novalidate'); ?>
        <?= form_open($const["route"] . '/update/' . $const["id"], $attributes); ?>
        <div class="card">
            <div class="card-header border-0">
                <div class="row align-items-center gy-3">
                    <div class="col-sm">
                        <h4 class="card-title mb-0 flex-grow-1">Edit <?= $const["item"]; ?></h4>
                    </div>
                </div>
            </div>
            <div class="card-body border border-dashed">
                <?= $this->include($const["viewfolder"] . "/form"); ?>
            </div>
            <div class="card-footer  border-0">
                <div class="hstack gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary btn-label waves-effect waves-light rounded-pill"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Update</button>
                    <a href="<?= site_url($const["route"]); ?>" class="btn btn-danger btn-label waves-effect waves-light rounded-pill"><i class="ri-close-line label-icon align-middle rounded-pill fs-16 me-2"></i> Cancel</a>
                </div>
            </div>
        </div>
        </form>
    </div>
    <!-- end row -->
    <!--  END CONTENT PART  -->
    <?= $this->endSection() ?>
    <?= $this->section("footerjs") ?>
    <script src="<?= site_url(); ?>assets/js/pages/form-validation.init.js"></script>
    <?= $this->endSection() ?>
