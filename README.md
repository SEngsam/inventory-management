# Inventory Management System

## Project Overview

This Inventory Management System is a web application built using Laravel and Livewire. It allows users to manage products, categories, brands, customers, sales, and returns efficiently. The system supports stock tracking and real-time updates using Livewire components for a smooth user experience.

---

## Features

- **Product Management**: Add, edit, and delete products with images, categories, brands, pricing, and stock quantities.
- **Category & Brand Management**: Organize products into categories and brands.
- **Customer Management**: Manage customer information.
- **Sales Management**: Create, update, and view sales with automatic stock deduction.
- **Returns Management**: Handle product returns and update stock accordingly.
- **Dashboard**: Visual summaries of total products, customers, sales, returns, and top-selling products.
- **Interactive UI**: Real-time form updates and validation with Livewire.
- **Reports**: Monthly sales charts and detailed transaction views.

---

## Tools & Technologies Used

- **Backend**: Laravel Framework (PHP)
- **Frontend**: Livewire (Laravel Livewire)
- **UI Framework**: Bootstrap 5 (AdminKit theme)
- **Database**: MySQL
- **Version Control**: Git & GitHub
- **Other Libraries**:
  - ApexCharts for interactive charts
  - Feather Icons for UI icons

---

## Functions & Inventory Management Explanation

### 1. Product Management Functions

- **Create/Update Product**:  
  Allows saving product details like name, price, category, brand, image, warranty, and stock quantity. Validations ensure data correctness.  
  Supports image upload and displays existing product images.

- **Reduce Stock**:  
  Inside the `Product` model, the `reduceStock` function reduces the available stock quantity after a sale is completed.

  ```php
  public function reduceStock($quantity)
  {
      $this->decrement('stock_quantity', $quantity);
  }
