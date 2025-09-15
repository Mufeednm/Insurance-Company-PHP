<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?><?= $const["items"]; ?><?= $this->endSection() ?>
<?= $this->section("headercss") ?>
<style>
    .mr-2
    {
        margin-right: 20px;
    }
</style>
<?= $this->endSection() ?>
<?= $this->section("headerjs") ?><?= $this->endSection() ?>
<?= $this->section("content") ?>
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
<!-- end row -->
<div class="row">
    <div class="col-lg-12">
        <?php $attributes = array('class' => 'needs-validation', 'novalidate' => 'novalidate'); ?>
        <?= form_open_multipart($const["route"].'/create', $attributes); ?>
        <div class="card">
            <div class="card-header border-0">
                <div class="row align-items-center gy-3">
                    <div class="col-sm">
                        <h4 class="card-title mb-0 flex-grow-1">New <?= $const["item"]; ?></h4>
                    </div>
                </div>
            </div>
            <div class="card-body border border-dashed">
                <?= form_open($const["route"].'/create'); ?>

            <?php if (session()->has('error')) : ?>
            <div class="alert alert-danger" role="alert">
                <ul style="margin-bottom:0px;">
                    <?php foreach (session('error') as $error) : ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif ?>

            <div id="permissionRows">
                <div class="permission-row">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label>Description</label>
                            <input type="text" class="form-control" name="description[]" placeholder="Description" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Permission Key</label>
                            <input type="text" class="form-control permissionKey" name="permissionKey[]" placeholder="Permission Key" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Group</label>
                            <input type="text" class="form-control" name="group[]" placeholder="Group Name" required>
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-center" style="padding-top: 25px;">
                            <button type="button" class="btn btn-success addRow mr-2">+</button>
                            <button type="button" class="btn btn-info copyRow mr-2">⧉</button>
                            <button type="button" class="btn btn-danger removeRow">-</button>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="card-footer  border-0">
                <div class="hstack gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary btn-label waves-effect waves-light rounded-pill"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Submit</button>
                    <a href="<?= site_url($const["route"]); ?>" class="btn btn-danger btn-label waves-effect waves-light rounded-pill"><i class="ri-close-line label-icon align-middle rounded-pill fs-16 me-2"></i> Cancel</a>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
<!-- end row -->
<?= $this->endSection() ?>
<?= $this->section("footerjs") ?>
<script src="<?= site_url(); ?>assets/js/pages/form-validation.init.js"></script>
<script>
function normalizePermissionKey(inputSelector) {
    var $input = $(inputSelector);
    if ($input.length === 0) {
        console.warn("Element " + inputSelector + " not found.");
        return;
    }
    var value = $input.val();
    value = $.trim(value);
    value = value.replace(/^\/+|\/+$/g, '');
    value = value.replace(/\s+/g, '');
    value = value.toLowerCase();
    value = value.replace(/\/{2,}/g, '/');
    $input.val(value);
}
</script>

<script>
$(document).ready(function(){

    // Add New Row
    $(document).on('click', '.addRow', function(){
        let newRow = $('.permission-row:first').clone();
        newRow.find('input').val('');
        $('#permissionRows').append(newRow);
    });

    // Copy Current Row
    $(document).on('click', '.copyRow', function(){
        let copiedRow = $(this).closest('.permission-row').clone();
        $('#permissionRows').append(copiedRow);
    });

    // Remove Row
    $(document).on('click', '.removeRow', function(){
        if ($('.permission-row').length > 1) {
            $(this).closest('.permission-row').remove();
        }
    });

    // Normalize permissionKey on blur
    $(document).on('blur', '.permissionKey', function(){
        let value = $(this).val();
        value = value.trim().toLowerCase().replace(/\s+/g, '').replace(/^\/+|\/+$/g, '');
        $(this).val(value);
    });

});
</script>
<?= $this->endSection() ?>