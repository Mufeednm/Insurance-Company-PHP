<?php
// $data is the Policy entity (App\Entities\Entity)
$isEdit = isset($const['id']) && !empty($const['id']);
$productIdVal = old('productId', $data->productId ?? '');
$startVal     = old('startDate', $data->startDate ?? '');
$endVal       = old('endDate', $data->endDate ?? '');
$statusVal    = old('status', $data->status ?? 'Active');
?>

<div class="row g-3">
  <div class="col-xxl-4 col-md-6">
    <label for="productId" class="form-label">Product</label>
    <select name="productId" id="productId" class="form-select" required>
      <option value="">Select Product</option>
      <?php foreach ($productOptions as $id => $name): ?>
        <option value="<?= (int)$id ?>" <?= $productIdVal == $id ? 'selected' : '' ?>><?= esc($name) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-xxl-3 col-md-4">
    <label class="form-label">Start Date</label>
    <input type="date" name="startDate" class="form-control" value="<?= esc($startVal) ?>" required>
  </div>

  <div class="col-xxl-3 col-md-4">
    <label class="form-label">End Date</label>
    <input type="date" name="endDate" class="form-control" value="<?= esc($endVal) ?>">
  </div>

  

  <div class="col-xxl-2 col-md-4">
    <label class="form-label">Reminder</label>
    <?php $reminderVal = $reminderVal ?? '1'; // default YES ?>
<select name="isReminder" class="form-select">
  <option value="1" <?= $reminderVal == '1' ? 'selected' : '' ?>> Yes</option>
  <option value="0" <?= $reminderVal == '0' ? 'selected' : '' ?>> No</option>
</select>
  </div>
</div>
