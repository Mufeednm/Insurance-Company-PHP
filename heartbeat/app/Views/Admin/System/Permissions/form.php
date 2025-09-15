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
<div class="row">
    <div class="col-xxl-3 col-md-6">
        <?= input('description', 'Description', 'text', $data, ['required' => true]); ?>
    </div>
    <div class="col-xxl-3 col-md-6">
        <?= input('permissionKey', 'Permission Key', 'text', $data, ['required' => true]); ?>
    </div>
    <div class="col-xxl-3 col-md-6">
        <?= input('group', 'Group', 'text', $data, ['required' => true]); ?>
    </div>
</div>