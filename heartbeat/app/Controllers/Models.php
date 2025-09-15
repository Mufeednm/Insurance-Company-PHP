<?php

    $this->login = new \App\Models\Login;
    $this->permissions = new \App\Models\Permissions;
    $this->rolePermissions = new \App\Models\RolePermissions;
    $this->roles = new \App\Models\Roles;
    
    $this->products = new \App\Models\Products;

    $this->customers = new \App\Models\Customers;

    $this->attributes = new \App\Models\Attributes;
    $this->policies = new \App\Models\Policies;
    $this->attributevalues = new \App\Models\AttributeValues;
    $this->policyattributevalues = new \App\Models\PolicyAttributeValues;
?>