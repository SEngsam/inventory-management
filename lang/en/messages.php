<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Messages
    |--------------------------------------------------------------------------
    |
    | Flash notices and validation messages shown by the inventory screens.
    |
    */

    'stock' => [
        'insufficient' => 'Insufficient stock for one or more products.',
        'insufficient_for' => 'Insufficient stock for :product.',
        'insufficient_detail' => 'Insufficient stock for :product. Available: :available, requested: :requested.',
    ],

    'items' => [
        'required' => 'At least one item is required.',
        'product_required' => 'Product is required.',
        'quantity_cost_positive' => 'Quantity and cost must be positive.',
        'select_product' => 'Please select at least one product.',
        'products_not_found' => 'One or more selected products were not found.',
    ],

    'sale' => [
        'saved' => 'Sale saved successfully.',
        'deleted' => 'Sale deleted successfully.',
    ],

    'return' => [
        'saved' => 'Return saved successfully.',
        'deleted' => 'Return deleted successfully.',
        'sale_not_found' => 'The selected sale was not found.',
        'invalid_items' => 'Invalid return items.',
        'exceeds_sold' => 'Return quantity cannot exceed the sold quantity.',
    ],

    'purchase' => [
        'created' => 'Purchase created successfully.',
        'updated' => 'Purchase updated successfully.',
    ],

    'invoice' => [
        'empty' => 'An invoice must have at least one item.',
        'bulk_deleted' => 'Selected invoices deleted successfully.',
    ],

    'invoice_settings' => [
        'saved' => 'Invoice settings saved.',
        'format_placeholder' => 'The format must include a zero-padding placeholder such as {000000}.',
    ],

    'product' => [
        'created' => 'Product created successfully.',
        'updated' => 'Product updated successfully.',
        'bulk_deleted' => 'Selected products deleted successfully.',
    ],

    'category' => [
        'created' => 'Category created successfully.',
        'updated' => 'Category updated successfully.',
        'bulk_deleted' => 'Selected categories deleted successfully.',
    ],

    'brand' => [
        'created' => 'Brand created successfully.',
        'updated' => 'Brand updated successfully.',
        'bulk_deleted' => 'Selected brands deleted successfully.',
    ],

    'unit' => [
        'created' => 'Unit created successfully.',
        'updated' => 'Unit updated successfully.',
        'bulk_deleted' => 'Selected units deleted successfully.',
    ],

    'customer' => [
        'created' => 'Customer created successfully.',
        'updated' => 'Customer updated successfully.',
        'deleted' => 'Customer deleted successfully.',
        'bulk_deleted' => 'Selected customers deleted successfully.',
    ],

    'supplier' => [
        'created' => 'Supplier created successfully.',
        'updated' => 'Supplier updated successfully.',
        'deleted' => 'Supplier deleted successfully.',
        'bulk_deleted' => 'Selected suppliers deleted successfully.',
    ],

    'currency' => [
        'created' => 'Currency added successfully.',
        'updated' => 'Currency updated successfully.',
        'deleted' => 'Currency deleted successfully.',
        'default_set' => 'Default currency updated.',
    ],

    'branding' => [
        'saved' => 'Company and branding settings saved.',
        'logo_removed' => 'Logo removed.',
    ],

    'settings' => [
        'saved' => 'Settings saved successfully.',
    ],

    'user' => [
        'created' => 'User created successfully.',
        'updated' => 'User updated successfully.',
        'deleted' => 'User deleted successfully.',
        'bulk_deleted' => 'Selected users deleted successfully.',
    ],

    'role' => [
        'created' => 'Role created successfully.',
        'updated' => 'Role updated successfully.',
        'deleted' => 'Role deleted successfully.',
        'admin_protected' => 'The admin role cannot be deleted.',
    ],

];
