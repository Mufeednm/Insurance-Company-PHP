<?php
// $data is Entity when editing, otherwise new Entity
$isEdit = isset($const['id']) && !empty($const['id']);
?>

<?php if (session()->has('error')) : ?>
    <div class="row">
        <div class="col">
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

<!-- Row 1 -->
<div class="row">
    <div class="col-xxl-4 col-md-4">
        <?= select('productId', 'Product', $productOptions ?? [], $data, ['required' => true]); ?>
    </div>

    <div class="col-xxl-4 col-md-4">
        <?= input('attributeName', 'Attribute Name', 'text', $data, ['required' => true]); ?>
    </div>

    <div class="col-xxl-4 col-md-4">
        <?= select('attributeType', 'Type', [
            'text' => 'Text',
            'date' => 'Date',
            'textarea' => 'Textarea',
            'select' => 'Select',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio',
            'number' => 'Number',
         
        ], $data, ['required' => true]); ?>
    </div>

    
</div>
<!-- Row 2 -->
<div class="row mt-3">

    <!-- Reminder (only for date type) -->
    <div class="col-xxl-4 col-md-4" id="reminderCol" style="display:none;">
        <?= select('isReminder', 'Reminder', ['1' => 'Yes', '0' => 'No'], $data, ['required' => false]); ?>
    </div>
    
    <div class="col-xxl-4 col-md-4">
        <?= select('isRequired', 'Required', ['1' => 'Yes', '0' => 'No'], $data, ['required' => true]); ?>
    </div>



    <div class="col-xxl-4 col-md-4">
        <?= input('attributeOrder', 'Order', 'number', $data, ['min' => 0]); ?>
    </div>

    <div class="col-xxl-4 col-md-4">
        <label class="form-label">Options (one per line or separated by | )</label>
        <?php
            $optsVal = null;
            if (!empty($data->options)) {
                $s = (string)$data->options;
                if (preg_match('/^\[\s*"(.*)"\s*\]$/', $s, $m)) {
                    $parts = explode('"|"', $m[1]);
                    $optsVal = implode("\n", $parts);
                } else {
                    $optsVal = $s;
                }
            }
        ?>
        <textarea name="options" class="form-control" rows="4" placeholder="Option one newline Option two"><?= esc(old('options', $optsVal)) ?></textarea>
        <small class="form-text text-muted">Only for select/checkbox/radio types. Stored as ["opt1"|"opt2"].</small>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const typeSelect = document.querySelector("[name='attributeType']");
    const reminderCol = document.getElementById("reminderCol");

    function toggleReminder() {
        if (typeSelect.value === "date") {
            reminderCol.style.display = "block";
        } else {
            reminderCol.style.display = "none";
            reminderCol.querySelector("select").value = "0"; // reset to No
        }
    }

    typeSelect.addEventListener("change", toggleReminder);
    toggleReminder(); // run on page load
});
</script>
