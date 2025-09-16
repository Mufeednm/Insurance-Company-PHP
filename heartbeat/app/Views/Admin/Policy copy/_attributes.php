<?php
if (!isset($attributes) || !is_array($attributes)) $attributes = [];
if (!isset($values) || !is_array($values)) $values = [];

function parseOptionsString($s) {
    $s = (string)$s;
    if ($s === '') return [];
    if (preg_match('/^\[\s*"(.*)"\s*\]$/', $s, $m)) return array_map('trim', explode('"|"', $m[1]));
    if (strpos($s, "|") !== false) return array_map('trim', explode("|", $s));
    if (strpos($s, "\n") !== false) return array_map('trim', preg_split("/\r\n|\n|\r/", $s));
    return [trim($s)];
}

if (empty($attributes)) {
    echo '<div class="alert alert-info">No attributes defined for this product.</div>';
    return;
}

$total = count($attributes);

// FIRST visual row: up to 3 attributes (col-md-4) so Product(col-md-3) + 3 attrs fit the top row
$firstRowCount = min(3, $total);
if ($firstRowCount > 0) {
    echo '<div class="row g-3">';
    for ($i = 0; $i < $firstRowCount; $i++) {
        $attr = $attributes[$i];
        $id = (int)$attr->attributeId;
        $label = esc($attr->attributeName);
        $type  = $attr->attributeType;
        $required = ((int)$attr->isRequired === 1);
        $opts = !empty($attr->options) ? parseOptionsString($attr->options) : [];
        $val = $values[$id] ?? null;
        $oldVal = old("attributes.$id");
        if ($oldVal !== null) $val = $oldVal;
        $placeholder = isset($attr->placeholder) && trim((string)$attr->placeholder) !== '' ? $attr->placeholder : $label;
        $requiredAttr = $required ? 'required' : '';

        echo '<div class="col-md-4 col-sm-6">';
          echo '<label class="form-label">'. $label . ($required ? ' <span class="text-danger">*</span>' : '') .'</label>';
          switch ($type) {
            case 'textarea':
              $v = is_array($val) ? implode("\n", $val) : ($val ?? '');
              echo '<textarea name="attributes['.$id.']" class="form-control" rows="3" '. $requiredAttr .' placeholder="'.esc($placeholder).'">'.esc($v).'</textarea>';
              break;
            case 'select':
              echo '<select name="attributes['.$id.']" class="form-select" '. $requiredAttr .'>';
              echo '<option value="" disabled '.((string)($val ?? '') === '' ? 'selected' : '').'>-- '.esc($placeholder).' --</option>';
              foreach ($opts as $o) { $sel = ((string)($val ?? '') === (string)$o) ? 'selected' : ''; echo '<option value="'.esc($o).'" '.$sel.'>'.esc($o).'</option>'; }
              echo '</select>';
              break;
            case 'checkbox':
              $existing = is_array($val) ? $val : (is_string($val) && $val !== '' ? explode('|', $val) : []);
              if (!empty($opts)) {
                foreach ($opts as $o) { $checked = in_array((string)$o, array_map('strval', $existing), true) ? 'checked' : ''; $cbId = 'attr_'.$id.'_'.preg_replace('/\W+/','', $o);
                  echo '<div class="form-check"><input class="form-check-input" id="'.esc($cbId).'" type="checkbox" name="attributes['.$id.'][]" value="'.esc($o).'" '.$checked.'><label class="form-check-label" for="'.esc($cbId).'">'.esc($o).'</label></div>'; }
              } else {
                echo '<input type="text" name="attributes['.$id.'][]" class="form-control" placeholder="'.esc($placeholder).'" '. $requiredAttr .'>';
                echo '<small class="text-muted d-block mt-1">Enter multiple values; saved separated by "|".</small>';
              }
              break;
            case 'radio':
              if (!empty($opts)) {
                foreach ($opts as $o) { $checked = ((string)$val === (string)$o) ? 'checked' : ''; $rbId = 'attr_'.$id.'_'.preg_replace('/\W+/','', $o);
                  echo '<div class="form-check"><input class="form-check-input" id="'.esc($rbId).'" type="radio" name="attributes['.$id.']" value="'.esc($o).'" '.$checked.' '. $requiredAttr .'><label class="form-check-label" for="'.esc($rbId).'">'.esc($o).'</label></div>'; }
              } else {
                echo '<input type="text" name="attributes['.$id.']" class="form-control" placeholder="'.esc($placeholder).'" '. $requiredAttr .'>';
              }
              break;
            case 'number':
              $v = $val ?? ''; echo '<input type="number" name="attributes['.$id.']" class="form-control" value="'.esc($v).'" placeholder="'.esc($placeholder).'" '. $requiredAttr .'>';
              break;
            case 'date':
              $v = $val ?? ''; echo '<input type="date" name="attributes['.$id.']" class="form-control" value="'.esc($v).'" '. $requiredAttr .'>';
              break;
            default:
              $v = is_array($val) ? implode('|', $val) : ($val ?? '');
              echo '<input type="text" name="attributes['.$id.']" class="form-control" value="'.esc($v).'" placeholder="'.esc($placeholder).'" '. $requiredAttr .'>';
              break;
          }
          if (!empty($opts) && in_array($type, ['select','radio','checkbox'])) echo '<small class="text-muted d-block mt-1">Options: '.esc(implode(', ', $opts)).'</small>';
        echo '</div>';
    }
    echo '</div>'; // end first row
}

// Remaining attributes -> rows of 4 (col-md-3)
if ($total > $firstRowCount) {
    $remaining = array_slice(array_values($attributes), $firstRowCount);
    $remTotal = count($remaining);

    for ($j = 0; $j < $remTotal; $j++) {
        if ($j % 4 === 0) echo '<div class="row g-3 mt-2">';
        $attr = $remaining[$j];
        $id = (int)$attr->attributeId;
        $label = esc($attr->attributeName);
        $type  = $attr->attributeType;
        $required = ((int)$attr->isRequired === 1);
        $opts = !empty($attr->options) ? parseOptionsString($attr->options) : [];
        $val = $values[$id] ?? null;
        $oldVal = old("attributes.$id");
        if ($oldVal !== null) $val = $oldVal;
        $placeholder = isset($attr->placeholder) && trim((string)$attr->placeholder) !== '' ? $attr->placeholder : $label;
        $requiredAttr = $required ? 'required' : '';

        echo '<div class="col-md-3 col-sm-6">';
          echo '<label class="form-label">'. $label . ($required ? ' <span class="text-danger">*</span>' : '') .'</label>';
          switch ($type) {
            case 'textarea':
              $v = is_array($val) ? implode("\n", $val) : ($val ?? '');
              echo '<textarea name="attributes['.$id.']" class="form-control" rows="3" '. $requiredAttr .' placeholder="'.esc($placeholder).'">'.esc($v).'</textarea>';
              break;
            case 'select':
              echo '<select name="attributes['.$id.']" class="form-select" '. $requiredAttr .'>';
              echo '<option value="" disabled '.((string)($val ?? '') === '' ? 'selected' : '').'>-- '.esc($placeholder).' --</option>';
              foreach ($opts as $o) { $sel = ((string)($val ?? '') === (string)$o) ? 'selected' : ''; echo '<option value="'.esc($o).'" '.$sel.'>'.esc($o).'</option>'; }
              echo '</select>';
              break;
            case 'checkbox':
              $existing = is_array($val) ? $val : (is_string($val) && $val !== '' ? explode('|', $val) : []);
              if (!empty($opts)) {
                foreach ($opts as $o) { $checked = in_array((string)$o, array_map('strval', $existing), true) ? 'checked' : ''; $cbId = 'attr_'.$id.'_'.preg_replace('/\W+/','', $o);
                  echo '<div class="form-check"><input class="form-check-input" id="'.esc($cbId).'" type="checkbox" name="attributes['.$id.'][]" value="'.esc($o).'" '.$checked.'><label class="form-check-label" for="'.esc($cbId).'">'.esc($o).'</label></div>'; }
              } else {
                echo '<input type="text" name="attributes['.$id.'][]" class="form-control" placeholder="'.esc($placeholder).'" '. $requiredAttr .'>';
                echo '<small class="text-muted d-block mt-1">Enter multiple values; saved separated by "|".</small>';
              }
              break;
            case 'radio':
              if (!empty($opts)) {
                foreach ($opts as $o) { $checked = ((string)$val === (string)$o) ? 'checked' : ''; $rbId = 'attr_'.$id.'_'.preg_replace('/\W+/','', $o);
                  echo '<div class="form-check"><input class="form-check-input" id="'.esc($rbId).'" type="radio" name="attributes['.$id.']" value="'.esc($o).'" '.$checked.' '. $requiredAttr .'><label class="form-check-label" for="'.esc($rbId).'">'.esc($o).'</label></div>'; }
              } else {
                echo '<input type="text" name="attributes['.$id.']" class="form-control" placeholder="'.esc($placeholder).'" '. $requiredAttr .'>';
              }
              break;
            case 'number':
              $v = $val ?? ''; echo '<input type="number" name="attributes['.$id.']" class="form-control" value="'.esc($v).'" placeholder="'.esc($placeholder).'" '. $requiredAttr .'>';
              break;
            case 'date':
              $v = $val ?? ''; echo '<input type="date" name="attributes['.$id.']" class="form-control" value="'.esc($v).'" '. $requiredAttr .'>';
              break;
            default:
              $v = is_array($val) ? implode('|', $val) : ($val ?? '');
              echo '<input type="text" name="attributes['.$id.']" class="form-control" value="'.esc($v).'" placeholder="'.esc($placeholder).'" '. $requiredAttr .'>';
              break;
          }
          if (!empty($opts) && in_array($type, ['select','radio','checkbox'])) echo '<small class="text-muted d-block mt-1">Options: '.esc(implode(', ', $opts)).'</small>';
        echo '</div>';
        if ($j % 4 === 3 || $j === $remTotal - 1) echo '</div>'; // close 4-col row
    }
} else {
    // if only firstRowCount attributes exist, ensure row closed (we already closed above)
}
