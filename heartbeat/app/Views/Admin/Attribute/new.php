<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?><?= $const["items"]; ?><?= $this->endSection() ?>
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
        <?= form_open($const["route"].'/create', $attributes); ?>
        <div class="card">
            <div class="card-header border-0">
                <div class="row align-items-center gy-3">
                    <div class="col-sm">
                        <h4 class="card-title mb-0 flex-grow-1">New <?= $const["item"]; ?></h4>
                    </div>
                    
                </div>
            </div>
            <div class="card-body border border-dashed">

                <div id="rows">
                    <?= $this->include($const["viewfolder"] . "/form"); ?>
                </div>

                <!-- <template id="product-row-template">
                     <div class="attribute-row mb-3">
                      
                         <hr>
                    </div>
                 </template> -->


                <?php if (session()->has('error')) : ?>
                    <div class="row mt-3">
                        <div mg="6" class="col">
                            <div class="alert alert-danger" role="alert">
                                <ul style="margin-bottom:0px;">
                                    <?php foreach (session('error') as $error) : ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
            <div class="card-footer  border-0">
                <div class="hstack gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary btn-label waves-effect waves-light rounded-pill"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
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
<script>
(function(){
    const rows = document.getElementById('rows');
    const tpl  = document.getElementById('product-row-template');
    const add  = document.getElementById('addRow');
    const rem  = document.getElementById('removeRow');

    add.addEventListener('click', function(){
        rows.appendChild(tpl.content.cloneNode(true));
    });

    rem.addEventListener('click', function(){
        const list = rows.querySelectorAll('.attribute-row');
        if (list.length > 1) {
            list[list.length - 1].remove();
        }
    });
})();


</script>
<script src="<?= site_url(); ?>assets/js/pages/form-validation.init.js"></script>
<?= $this->endSection() ?>
