<?php

use App\Livewire\Sales\ReturnShow;
use App\Livewire\Settings\ManageCurrencies;
use App\Livewire\Settings\ManageSettings;
use App\Livewire\Users\RoleManager;
use App\Livewire\Users\UserManager;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoutingController;
use App\Livewire\DashboardPanel;
use App\Livewire\Invoices\CreateInvoice;
use App\Livewire\Invoices\InvoiceList;
use App\Livewire\Invoices\InvoiceShow;
use App\Livewire\Products\ProductForm;
use App\Livewire\Products\ProductList;
use App\Livewire\Products\BrandManager;
use App\Livewire\Products\CategoryManager;
use App\Livewire\Products\UnitManager;
use App\Livewire\Purchases\PurchaseForm;
use App\Livewire\Purchases\PurchaseList;
use App\Livewire\Purchases\PurchaseShow;
use App\Livewire\Sales\ReturnForm;
use App\Livewire\Sales\ReturnList;
use App\Livewire\Sales\SaleForm;
use App\Livewire\Sales\SaleList;
use App\Livewire\Sales\SaleShow;
use App\Livewire\Suppliers\SupplierForm;
use App\Livewire\Suppliers\SupplierList;
use App\Models\Customer;
use App\Models\Supplier;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Static Livewire Routes (First)
|--------------------------------------------------------------------------
*/

// Dashboard

Route::middleware(['auth'])->group(function () {
    Route::get('/users/roles', RoleManager::class)->name('users.roles');

    Route::get('/users', UserManager::class)->name('users.index');
    Route::prefix('settings')->group(function () {
        Route::get('/', ManageSettings::class)->name('settings.general');

        Route::get('/currencies', ManageCurrencies::class)->name('settings.currencies');
    });



    Route::get('/', DashboardPanel::class)->name('root');

    // Product routes
    Route::prefix('products')->name('product.')->group(function () {
        Route::get('/units', UnitManager::class);
        Route::get('/categories', CategoryManager::class);
        Route::get('/brands', BrandManager::class);
    });
    Route::get('/products', ProductList::class)->name('products.list');
    Route::get('/products/create', ProductForm::class)->name('products.create');
    Route::get('/products/{id}/edit', ProductForm::class)->name('products.edit');

    // Purchases
    Route::get('/purchases', PurchaseList::class)->name('purchases.index');
    Route::get('/purchases/create', PurchaseForm::class)->name('purchases.create');
    Route::get('/purchases/edit/{purchaseId}', PurchaseForm::class)->name('purchases.edit');
    Route::get('/purchases/{purchaseId}', PurchaseShow::class)->name('purchases.show');

    // Sales
    Route::get('/sales', SaleList::class)->name('sales.index');
    Route::get('/sales/create', SaleForm::class)->name('sales.create');
    Route::get('/sales/edit/{id}', SaleForm::class)->name('sales.edit');
    Route::get('/sales/{id}', SaleShow::class)->name('sales.show');

    // Sale Returns

    Route::get('/returns', ReturnList::class)->name('sale-returns.index');
    Route::get('/returns/create', action: ReturnForm::class)->name('sale-returns.create');
    Route::get('/returns/edit/{return}', ReturnForm::class)->name('sale-returns.edit');
    Route::get('/returns/{return}', ReturnShow::class)->name('sale-returns.show');


    // Suppliers
    Route::get('/suppliers', SupplierList::class)->name('suppliers.index');
    Route::get('/suppliers/create', SupplierForm::class)->name('suppliers.create');
    Route::get('/suppliers/edit/{supplier}', fn($supplier) => view('inventory.suppliers.supplier-form', ['supplier' => $supplier]))->name('supplier.edit');

    // Customers
    Route::get('/customers', fn() => view('inventory.customers.customer-list'))->name('customer.index');
    Route::get('/customers/create', fn() => view('inventory.customers.customer-form', ['customer' => null]))->name('customers.create');
    Route::get('/customers/edit/{customer}', function (Customer $customer) {
        return view('inventory.customers.customer-form', ['customer' => $customer]);
    })->name('customer.edit');

    // Invoices
    Route::get('/invoices', InvoiceList::class)->name('invoices.index');
    Route::get('/invoice/create', CreateInvoice::class)->name('invoices.create');
    Route::get('/invoices/{invoice}', InvoiceShow::class)->name('invoices.show');


    /*
|--------------------------------------------------------------------------
| Dynamic Catch-All Routes (Last)
|--------------------------------------------------------------------------
*/
    Route::middleware('auth')->group(function () {
        Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
        Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
        Route::get('{any}', [RoutingController::class, 'root'])->name('any');
    });
});
