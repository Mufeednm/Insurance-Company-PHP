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
    echo '<div class="col-12 attr-col"><div class="alert alert-info mb-0">No attributes defined for this product.</div></div>';
    return;
}

foreach (array_values($attributes) as $attr) {
    $id = (int)$attr->attributeId;
    $label = $attr->attributeName ?? "Attribute $id";
    $type  = strtolower($attr->attributeType ?? 'text');
    $required = ((int)($attr->isRequired ?? 0) === 1);
    $opts = !empty($attr->options) ? parseOptionsString($attr->options) : [];
    $val = $values[$id] ?? null;
    $oldVal = old("attributes.$id");
    if ($oldVal !== null) $val = $oldVal;
    $placeholder = isset($attr->placeholder) && trim((string)$attr->placeholder) !== '' ? $attr->placeholder : $label;
    $requiredAttr = $required ? 'required' : '';

    // textarea should be flexible; others fixed to 3-col
    $colClass = ($type === 'textarea') ? 'col attr-col' : 'col-md-3 col-sm-6 attr-col';

    echo '<div class="'. $colClass .'">';
      echo '<label class="form-label">'. esc($label) . ($required ? ' <span class="text-danger">*</span>' : '') .'</label>';

      switch ($type) {
        case 'textarea':
          $v = is_array($val) ? implode("\n", $val) : ($val ?? '');
          echo '<textarea name="attributes['.$id.']" class="form-control" rows="2" '. $requiredAttr .' placeholder="'.esc($placeholder).'">'.esc($v).'</textarea>';
          break;

        case 'select':
          echo '<select name="attributes['.$id.']" class="form-select" '. $requiredAttr .'>';
          echo '<option value="" '.((string)($val ?? '') === '' ? 'selected' : '').'>-- '.esc($placeholder).' --</option>';
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
              $checked = in_array((string)$o, array_map('strval', $existing), true) ? 'checked' : '';
              $cbId = 'attr_'.$id.'_'.preg_replace('/\W+/','', $o);
              echo '<div class="form-check">';
              echo '<input class="form-check-input" id="'.esc($cbId).'" type="checkbox" name="attributes['.$id.'][]" value="'.esc($o).'" '.$checked.'>';
              echo '<label class="form-check-label" for="'.esc($cbId).'">'.esc($o).'</label>';
              echo '</div>';
            }
          } else {
            echo '<input type="text" name="attributes['.$id.'][]" class="form-control" placeholder="'.esc($placeholder).'" '. $requiredAttr .'>';
            echo '<small class="text-muted d-block mt-1">Enter multiple values; saved separated by "|".</small>';
          }
          break;

        case 'radio':
          if (!empty($opts)) {
            foreach ($opts as $o) {
              $checked = ((string)$val === (string)$o) ? 'checked' : '';
              $rbId = 'attr_'.$id.'_'.preg_replace('/\W+/','', $o);
              echo '<div class="form-check">';
              echo '<input class="form-check-input" id="'.esc($rbId).'" type="radio" name="attributes['.$id.']" value="'.esc($o).'" '.$checked.' '. $requiredAttr .'>';
              echo '<label class="form-check-label" for="'.esc($rbId).'">'.esc($o).'</label>';
              echo '</div>';
            }
          } else {
            echo '<input type="text" name="attributes['.$id.']" class="form-control" placeholder="'.esc($placeholder).'" '. $requiredAttr .'>';
          }
          break;

        case 'number':
          $v = $val ?? '';
          $numericAttrs = 'inputmode="numeric" pattern="\\d*" oninput="this.value=this.value.replace(/[^\\d]/g,\'\');"';
          echo '<input type="text" name="attributes['.$id.']" class="form-control" value="'.esc($v).'" placeholder="'.esc($placeholder).'" '. $numericAttrs .' '. $requiredAttr .'>';
          break;

        case 'date':
          $v = $val ?? '';
          echo '<input type="date" name="attributes['.$id.']" class="form-control" value="'.esc($v).'" '. $requiredAttr .'>';
          break;

        case 'text':
        default:
          $v = is_array($val) ? implode('|', $val) : ($val ?? '');
          echo '<input type="text" name="attributes['.$id.']" class="form-control" value="'.esc($v).'" placeholder="'.esc($placeholder).'" '. $requiredAttr .'>';
          break;
      }

      if (!empty($opts) && in_array($type, ['select','radio','checkbox'])) {
        echo '<small class="text-muted d-block mt-1">Options: '.esc(implode(', ', $opts)).'</small>';
      }

    echo '</div>'; // col
}
