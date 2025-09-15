<?php

function input(string $name, string $label, string $type = 'text', $data = null, array $attributes = []): string
{
    $id = $attributes['id'] ?? $name;
    $value = old($name, $data->$name ?? '');
    $placeholder = $attributes['placeholder'] ?? "Enter {$label}";
    $required = $attributes['required'] ?? true;
    $requiredAttr = $required ? 'required' : '';

    $extraAttrs = "";

    // Add type-specific attributes
    if ($type === 'number' || $type === 'date') {
        if (isset($attributes['min'])) {
            $extraAttrs .= ' min="' . $attributes['min'] . '"';
        }
        if (isset($attributes['max'])) {
            $extraAttrs .= ' max="' . $attributes['max'] . '"';
        }
    }
    if ($type === 'number' && isset($attributes['step'])) {
        $extraAttrs .= ' step="' . $attributes['step'] . '"';
    }

    // Add any additional attributes
    foreach ($attributes as $key => $val) {
        if (!in_array($key, ['placeholder', 'required', 'min', 'max', 'step', 'id'])) {
            $extraAttrs .= ' ' . $key . '="' . $val . '"';
        }
    }

    return <<<HTML
    <div class="mb-3">
        <label for="{$id}" class="form-label">{$label}</label>
        <input type="{$type}" class="form-control" name="{$name}" id="{$id}" value="{$value}" placeholder="{$placeholder}" {$requiredAttr}{$extraAttrs}>
        <div class="invalid-feedback">Please enter a valid {$label}.</div>
    </div>
HTML;
}

function select(
    string $name,
    string $label,
    array $options = [],
    $data = null,
    array $attributes = [],
    string $optionKey = '',
    string $optionValue = ''
): string
{
    $id = $attributes['id'] ?? $name;
    if ($id === 'status') {
        $id = 'status_' . rand(10000, 99999);
    }
    $placeholder = $attributes['placeholder'] ?? 'Please Select';
    $required = $attributes['required'] ?? true;
    $requiredAttr = $required ? 'required' : '';
    $selectedValue = old($name, $data->$name ?? '');

    // Extra attributes
    $extraAttrs = '';
    foreach ($attributes as $key => $val) {
        if (!in_array($key, ['placeholder', 'required', 'id'])) {
            $extraAttrs .= " {$key}=\"{$val}\"";
        }
    }

    // Build <option> list
    $optionHtml = "<option value=\"\">{$placeholder}</option>\n";

    foreach ($options as $key => $val) {
        // If it's dynamic, extract values using $optionKey/$optionValue
        if (is_object($val)) {
            $value = $val->{$optionKey};
            $text  = $val->{$optionValue};
        } elseif (is_array($val)) {
            $value = $val[$optionKey];
            $text  = $val[$optionValue];
        } else {
            $value = $key;
            $text  = $val;
        }

        $selected = ($selectedValue == $value) ? 'selected' : '';
        $optionHtml .= "<option value=\"{$value}\" {$selected}>{$text}</option>\n";
    }

    return <<<HTML
    <div class="mb-3">
        <label for="{$id}" class="form-label">{$label}</label>
        <select class="form-select" name="{$name}" id="{$id}" {$requiredAttr}{$extraAttrs}>
            {$optionHtml}
        </select>
        <div class="invalid-feedback">Please select a valid {$label}.</div>
    </div>
HTML;
}

function fileInput(string $name, string $label, $data = null, array $attributes = []): string
{
    $id = $attributes['id'] ?? $name;
    $accept = $attributes['accept'] ?? ''; // e.g., image/*, .pdf, etc.
    $existingFile = $data->$name ?? '';
    $replacementMsg = '';
    
    // Only required if no file already exists
    $isRequired = ($existingFile === '') && ($attributes['required'] ?? true);
    $requiredAttr = $isRequired ? 'required' : '';
    $acceptAttr = $accept ? 'accept="' . $accept . '"' : '';

    // Show warning if editing and file exists
    if ($existingFile) {
        $replacementMsg = <<<HTML
        <div class="form-text text-warning">
            A file is already uploaded. Uploading a new file will replace the existing one.
        </div>
HTML;
    }

    return <<<HTML
    <div class="mb-3">
        <label for="{$id}" class="form-label">{$label}</label>
        <input type="file" class="form-control" id="{$id}" name="{$name}" {$requiredAttr} {$acceptAttr}>
        {$replacementMsg}
        <div class="invalid-feedback">Please upload a valid {$label}.</div>
    </div>
HTML;
}