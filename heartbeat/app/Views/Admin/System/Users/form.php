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
        <?= input('userName', 'User Name', 'text', $data, ['required' => true]); ?>
    </div>
    <div class="col-xxl-3 col-md-6">
        <?= input('mobileNumber', 'Mobile Number', 'number', $data, ['step' => 1, 'required' => false]); ?>
    </div>
    <div class="col-xxl-3 col-md-6">
        <?= input('emailAddress', 'Email Address', 'email', $data, ['required' => false]); ?>
    </div>
    <div class="col-xxl-3 col-md-6">
        <?= input('pushToken', 'Push Token', 'text', $data, ['required' => false]); ?>
    </div>
    <div class="col-xxl-3 col-md-6">
        <?= select('roleId', 'Role', $roles, $data, ['required' => true], 'roleId', 'roleName'); ?>
    </div>
    <div class="col-xxl-3 col-md-6">
        <div>
            <label for="password">Password <?php if(!$data->userName): ?><span style="color:#ff0000;"><sup>*</sup></span><?php endif; ?></label>
            <div class="form-icon">
                <input type="password" class="form-control form-control-icon" name="password" placeholder="Password" <?php if ($data->userName == "") echo "required"; ?> value="<?= old('password'); ?>">
                <i class="ri-lock-line"></i>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-md-6">
        <div>
            <label for="password_confirmation">Confirm Password <?php if(!$data->userName): ?><span style="color:#ff0000;"><sup>*</sup></span><?php endif; ?></label>
            <div class="form-icon">
                <input type="password" class="form-control form-control-icon" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" <?php if ($data->userName == "") echo "required"; ?> value="<?= old('password_confirmation'); ?>">
                <i class="ri-lock-line"></i>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-md-6">
        <?= select('status', 'Status', ['Active' => 'Active', 'InActive' => 'InActive'], $data, ['required' => true]); ?>
    </div>
</div>