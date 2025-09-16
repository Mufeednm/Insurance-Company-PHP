<?php
// $data is the Policy entity (App\Entities\Entity)
$isEdit = isset($const['id']) && !empty($const['id']);
$productIdVal = old('productId', $data->productId ?? '');
?>

<div class="row g-3 align-items-start" id="attributes-row">
  <!-- Product column -->
  <div class="col-md-3 col-sm-6" id="product-col">
    <label for="productId" class="form-label">Product</label>
    <select name="productId" id="productId" class="form-select" required>
      <option value="">Select Product</option>
      <?php foreach ($productOptions as $id => $name): ?>
        <option value="<?= (int)$id ?>" <?= $productIdVal == $id ? 'selected' : '' ?>><?= esc($name) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Customer Name -->
  <div class="col-md-3 col-sm-6">
    <label for="customerName" class="form-label">Customer Name</label>
    <input type="text" name="customerName" id="customerName" class="form-control"
    placeholder="customerName" required>
  </div>

  <!-- Customer Phone -->
  <div class="col-md-3 col-sm-6">
    <label for="customerphone" class="form-label">Customer Phone</label>
    <input type="tel" name="customerphone" id="customerphone" class="form-control"
            placeholder=" phone number" required>
  </div>

  <!-- Attributes placeholder (will expand to fill remaining space) -->
  <div id="attributes-placeholder" class="col-md-3 col-sm-6">
    <!-- Placeholder or dynamically injected attributes -->
  </div>
</div>
