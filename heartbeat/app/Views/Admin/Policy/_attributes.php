<?php
if (!isset($attributes) || !is_array($attributes)) {
    $attributes = [];
}
if (!isset($values) || !is_array($values)) {
    $values = [];
}

function parseOptionsString($s) {
    $s = (string)$s;
    if ($s === '') return [];
    if (preg_match('/^\[\s*"(.*)"\s*\]$/', $s, $m)) {
        return array_map('trim', explode('"|"', $m[1]));
    }
    if (strpos($s, "|") !== false) return array_map('trim', explode("|", $s));
    if (strpos($s, "\n") !== false) return array_map('trim', preg_split("/\r\n|\n|\r/", $s));
    return [trim($s)];
}

if (empty($attributes)) {
    echo '<div class="alert alert-info">No attributes defined for this product.</div>';
    return;
}

foreach (array_values($attributes) as $i => $attr):
    if ($i % 4 === 0) echo '<div class="row g-3">'; // open row every 4 items

    $id = (int)$attr->attributeId;
    $label = esc($attr->attributeName);
    $type  = $attr->attributeType;
    $required = ((int)$attr->isRequired === 1);
    $opts = !empty($attr->options) ? parseOptionsString($attr->options) : [];
    $val = $values[$id] ?? null;
    $oldVal = old("attributes.$id");
    if ($oldVal !== null) $val = $oldVal;

    echo '<div class="col-md-3">'; // 4 per row
      echo '<label class="form-label">'. $label . ($required ? ' <span class="text-danger">*</span>' : '') .'</label>';

      switch ($type) {
        case 'textarea':
          $v = is_array($val) ? implode("\n", $val) : ($val ?? '');
          echo '<textarea name="attributes['.$id.']" class="form-control" rows="3" '.($required?'required':'').'>'.esc($v).'</textarea>';
          break;

        case 'select':
          echo '<select name="attributes['.$id.']" class="form-select" '.($required?'required':'').'>';
          echo '<option value="">-- Select --</option>';
          foreach ($opts as $o) {
            $sel = ((string)($val ?? '') === (string)$o) ? 'selected' : '';
            echo '<option value="'.esc($o).'" '.$sel.'>'.esc($o).'</option>';
          }
          echo '</select>';
          break;

        case 'checkbox':
          $existing = is_array($val) ? $val : (is_string($val) && $val !== '' ? explode('|', $val) : []);
          if (!empty($opts)) {
            foreach ($opts as $o) {
              $checked = in_array($o, $existing, true) ? 'checked' : '';
              echo '<div class="form-check">';
              echo '<input class="form-check-input" type="checkbox" name="attributes['.$id.'][]" value="'.esc($o).'" '.$checked.'>';
              echo '<label class="form-check-label">'.esc($o).'</label>';
              echo '</div>';
            }
          } else {
            echo '<input type="text" name="attributes['.$id.'][]" class="form-control" placeholder="Enter values" '.($required?'required':'').'>';
          }
          break;

        case 'radio':
          if (!empty($opts)) {
            foreach ($opts as $o) {
              $checked = ((string)$val === (string)$o) ? 'checked' : '';
              echo '<div class="form-check">';
              echo '<input class="form-check-input" type="radio" name="attributes['.$id.']" value="'.esc($o).'" '.$checked.'>';
              echo '<label class="form-check-label">'.esc($o).'</label>';
              echo '</div>';
            }
          } else {
            echo '<input type="text" name="attributes['.$id.']" class="form-control" '.($required?'required':'').'>';
          }
          break;

        case 'number':
          $v = $val ?? '';
          echo '<input type="number" name="attributes['.$id.']" class="form-control" value="'.esc($v).'" '.($required?'required':'').'>';
          break;

        case 'date':
          $v = $val ?? '';
          echo '<input type="date" name="attributes['.$id.']" class="form-control" value="'.esc($v).'" '.($required?'required':'').'>';
          break;

        case 'text':
        default:
          $v = is_array($val) ? implode('|', $val) : ($val ?? '');
          echo '<input type="text" name="attributes['.$id.']" class="form-control" value="'.esc($v).'" '.($required?'required':'').'>';
          break;
      }

      if (!empty($opts) && in_array($type, ['select','radio','checkbox'])) {
        echo '<small class="text-muted d-block mt-1">Options: '.esc(implode(', ', $opts)).'</small>';
      }

    echo '</div>'; // col

    if ($i % 4 === 3 || $i === count($attributes)-1) echo '</div>'; // close row
endforeach;
