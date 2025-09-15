<?= $this->extend("Layouts/default") ?>
<?= $this->section("title") ?>Manage Permissions for <?= $const["item"]; ?><?= $this->endSection() ?>
<?= $this->section("headercss") ?>
<style>
    .form-check-input {
        position: inherit;
        margin-top: 0px !important;
    }
    .search-box {
        width: 250px;
    }
</style>
<?= $this->endSection() ?>
<?= $this->section("headerjs") ?><?= $this->endSection() ?>
<?= $this->section("content") ?>

<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h6>Manage Permissions for: <?= esc($role->roleName) ?></h6>

    <input type="text" id="groupSearch" class="form-control search-box" placeholder="Search Groups...">
</div>

<?= form_open("admin/system/roles/updatepermissions/{$role->roleId}") ?>

<div class="row" id="permissionCards">
<?php
// Group permissions by group column
$groupedPermissions = [];
foreach ($globalPermissions as $permission) {
    $groupName = $permission->group ?? 'Others';
    $groupedPermissions[$groupName][] = $permission;
}
?>

<?php foreach ($groupedPermissions as $groupName => $permissions): 
    $groupClass = 'group-' . md5($groupName);
?>
    <div class="col-md-3 mb-4 permission-card" data-group="<?= strtolower($groupName) ?>">
        <div class="card border shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #67595e; color: #ffffff;">
                <strong><?= esc($groupName) ?></strong>
                <div>
                    <input type="checkbox" class="form-check-input master-checkbox" data-group="<?= $groupClass ?>">
                </div>
            </div>
            <div class="card-body">
                <?php foreach ($permissions as $perm): ?>
                    <div class="form-check">
                        <input class="form-check-input <?= $groupClass ?>" 
                            type="checkbox" 
                            name="permissions[]" 
                            value="<?= $perm->permissionId ?>" 
                            id="perm<?= $perm->permissionId ?>"
                            <?= in_array($perm->permissionId, $assignedPermissionIds) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="perm<?= $perm->permissionId ?>">
                            <?= esc($perm->description) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<div class="text-center mt-4">
    <button type="submit" class="btn btn-primary btn-lg">Save Permissions</button>
</div>

<?= form_close() ?>

<?= $this->endSection() ?>
<?= $this->section("footerjs") ?>

<script>
$(document).ready(function() {
    $('.master-checkbox').change(function(){
        let groupClass = '.' + $(this).data('group');
        $(groupClass).prop('checked', $(this).is(':checked'));
    });

    $('#groupSearch').on('keyup', function(){
        let searchText = $(this).val().toLowerCase();

        $('.permission-card').each(function(){
            let groupName = $(this).data('group');
            if(groupName.includes(searchText)){
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>

<?= $this->endSection() ?>