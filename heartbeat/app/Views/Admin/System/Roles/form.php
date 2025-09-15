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
        <?= input('roleName', 'Role Name', 'text', $data, ['required' => true]); ?>
    </div>
    <div class="col-xxl-3 col-md-6">
        <?= select('isAdmin', 'is Admin ?', ["1" => "Yes", "0" => "No"], $data, ['required' => true]); ?>
    </div>
</div>