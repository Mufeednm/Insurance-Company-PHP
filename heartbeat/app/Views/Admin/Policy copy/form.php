<?php
// $data is the Policy entity (App\Entities\Entity)
$isEdit = isset($const['id']) && !empty($const['id']);
$productIdVal = old('productId', $data->productId ?? '');
$startVal     = old('startDate', $data->startDate ?? '');
$endVal       = old('endDate', $data->endDate ?? '');
$statusVal    = old('status', $data->status ?? 'Active');
?>

<!-- parent row: children must be col-* -->
<div class="row g-5 align-items-start">
  <!-- Product takes 1 column in the first row -->
  <div class="col-md-3 col-sm-6">
    <label for="productId" class="form-label">Product</label>
    <select name="productId" id="productId" class="form-select" required>
      <option value="">Select Product</option>
      <?php foreach ($productOptions as $id => $name): ?>
        <option value="<?= (int)$id ?>" <?= $productIdVal == $id ? 'selected' : '' ?>><?= esc($name) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- IMPORTANT: attributes-wrapper must BE a column -->
  <div id="attributes-wrapper" class="col-md-9 col-sm-12">
    <?php if (!empty($attributes) && is_array($attributes)): ?>
        <?= $this->include($const['viewfolder'] . '_attributes'); ?>
    <?php else: ?>
        <div class="mb-2 text-muted">Select a product to load attributes.</div>
    <?php endif; ?>
  </div>
</div>
