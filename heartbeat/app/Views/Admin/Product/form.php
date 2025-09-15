<?php
// Use array names on New (bulk) and plain names on Edit.
$isEdit      = isset($const['id']) && !empty($const['id']);
$nameField   = $isEdit ? 'name' : 'name[]';
$statusField = $isEdit ? 'status' : 'status[]';
?>

<?php if (session()->has('error')) : ?>
    <div class="row">
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

<div class="row product-row">
    <div class="col-xxl-6 col-md-6">
        <?= input($nameField, 'Product Name', 'text', $data, ['required' => true]); ?>
    </div>
    <div class="col-xxl-3 col-md-6">
        <?= select($statusField, 'Status', ['1' => 'Active', '0' => 'Inactive'], $data, ['required' => true]); ?>
    </div>
</div>
