<?php
// $data is the Policy entity (App\Entities\Entity) or array
$isEdit = isset($const['id']) && !empty($const['id']);

// helper: prefer old() then entity property
function fv($name, $dataObj, $default = '') {
    $v = old($name);
    if ($v !== null && $v !== '') return $v;
    if (is_object($dataObj) && isset($dataObj->{$name})) return $dataObj->{$name};
    if (is_array($dataObj) && isset($dataObj[$name])) return $dataObj[$name];
    return $default;
}

$productIdVal = fv('productId', $data, '');
?>

<div class="row g-3 align-items-start" id="attributes-row">
  <!-- Product column -->
  <div class="col-md-3 col-sm-6" id="product-col">
    <label for="productId" class="form-label">Product</label>
    <select name="productId" id="productId" class="form-select" required>
      <option value="">Select Product</option>
      <?php foreach ($productOptions as $id => $name): 
            $sel = ((string)$productIdVal === (string)$id) ? 'selected' : '';
      ?>
        <option value="<?= (int)$id ?>" <?= $sel ?>><?= esc($name) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  
  <div class="col-md-3 col-sm-6">
  <label for="policyNumber" class="form-label">Policy Number</label>
  <input type="text" name="policyNumber" id="policyNumber" class="form-control"
         value="<?= esc(fv('policyNumber', $data)) ?>"
         placeholder="Policy Number" required
         oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
         pattern="[0-9]*" inputmode="numeric">
</div>



  <!-- Customer Name -->
  <div class="col-md-3 col-sm-6">
    <label for="customerName" class="form-label">Customer Name</label>
    <input type="text" name="customerName" id="customerName" class="form-control"
      value="<?= esc(fv('customerName', $data)) ?>" placeholder="Customer Name" required>
  </div>

  <!-- Customer Phone -->
  <div class="col-md-3 col-sm-6">
    <label for="customerphone" class="form-label">Customer Phone</label>
    <input type="text" name="customerphone" id="customerphone" class="form-control"
    value="<?= esc(fv('customerphone', $data)) ?>"
    oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
    placeholder="Phone number" required
    pattern="[0-9]*" inputmode="numeric">
      
  </div>

  <!-- Attributes placeholder (will be filled with .attr-col columns) -->
  <div id="attributes-placeholder" class="col-md-3 col-sm-6">
    <!-- server or JS will inject attribute col blocks here -->
  </div>
</div>

<!-- Example static fields row (policy number, status) -->

</div>
