<?php

use App\Livewire\Customers\CustomerForm;
// Livewire Components
use App\Livewire\Customers\CustomerList;
// Users
use App\Livewire\DashboardPanel;
use App\Livewire\Invoices\CreateInvoice;
// Settings
use App\Livewire\Invoices\InvoiceList;
// Products
use App\Livewire\Invoices\InvoiceShow;
use App\Livewire\Products\BrandManager;
use App\Livewire\Products\CategoryManager;
use App\Livewire\Products\ProductForm;
use App\Livewire\Products\ProductList;
// Purchases
use App\Livewire\Products\UnitManager;
use App\Livewire\Purchases\PurchaseForm;
use App\Livewire\Purchases\PurchaseList;
// Sales
use App\Livewire\Purchases\PurchaseShow;
use App\Livewire\Sales\ReturnForm;
use App\Livewire\Sales\ReturnList;
// Sale Returns
use App\Livewire\Sales\ReturnShow;
use App\Livewire\Sales\SaleForm;
use App\Livewire\Sales\SaleList;
// Suppliers
use App\Livewire\Sales\SaleShow;
use App\Livewire\Settings\ManageCompanyBranding;
// Customers
use App\Livewire\Settings\ManageCurrencies;
use App\Livewire\Settings\ManageInvoiceSettings;
// Invoices
use App\Livewire\Suppliers\SupplierForm;
use App\Livewire\Suppliers\SupplierList;
use App\Livewire\Users\RoleManager;
use App\Livewire\Users\UserManager;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'access.control'])->group(function () {

    // Dashboard
    Route::get('/', DashboardPanel::class)
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */
    Route::get('/users', UserManager::class)
        ->name('users.index');

    Route::get('/users/roles', RoleManager::class)
        ->name('users.roles');

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->name('settings.')->group(function () {

        Route::get('/branding', ManageCompanyBranding::class)
            ->name('general');

        Route::get('/currencies', ManageCurrencies::class)
            ->name('currencies');
        Route::get('/invoices', ManageInvoiceSettings::class)
            ->name('settings.invoices');
    });

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */
    Route::prefix('products')->name('products.')->group(function () {

        Route::get('/', ProductList::class)
            ->name('list');

        Route::get('/create', ProductForm::class)
            ->name('create');

        Route::get('/edit/{product}', ProductForm::class)
            ->name('edit');

        Route::get('/units', UnitManager::class)
            ->name('units');

        Route::get('/categories', CategoryManager::class)
            ->name('categories');

        Route::get('/brands', BrandManager::class)
            ->name('brands');
    });

    /*
    |--------------------------------------------------------------------------
    | Purchases
    |--------------------------------------------------------------------------
    */
    Route::prefix('purchases')->name('purchases.')->group(function () {

        Route::get('/', PurchaseList::class)
            ->name('index');

        Route::get('/create', PurchaseForm::class)
            ->name('create');

        Route::get('/edit/{purchaseId}', PurchaseForm::class)
            ->name('edit');

        Route::get('/{purchaseId}', PurchaseShow::class)
            ->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Sales
    |--------------------------------------------------------------------------
    */
    Route::prefix('sales')->name('sales.')->group(function () {

        Route::get('/', SaleList::class)
            ->name('index');

        Route::get('/create', SaleForm::class)
            ->name('create');

        Route::get('/edit/{id}', SaleForm::class)
            ->name('edit');

        Route::get('/{id}', SaleShow::class)
            ->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Sale Returns
    |--------------------------------------------------------------------------
    */
    Route::prefix('returns')->name('sale-returns.')->group(function () {

        Route::get('/', ReturnList::class)
            ->name('index');

        Route::get('/create', ReturnForm::class)
            ->name('create');

        Route::get('/edit/{return}', ReturnForm::class)
            ->name('edit');

        Route::get('/{return}', ReturnShow::class)
            ->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Suppliers
    |--------------------------------------------------------------------------
    */
    Route::prefix('suppliers')->name('suppliers.')->group(function () {

        Route::get('/', SupplierList::class)
            ->name('index');

        Route::get('/create', SupplierForm::class)
            ->name('create');

        Route::get('/edit/{supplier}', SupplierForm::class)
            ->name('edit');
    });

    /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    */
    Route::prefix('customers')->name('customers.')->group(function () {

        Route::get('/', CustomerList::class)
            ->name('index');

        Route::get('/create', CustomerForm::class)
            ->name('create');

        Route::get('/edit/{customer}', CustomerForm::class)
            ->name('edit');
    });

    /*
    |--------------------------------------------------------------------------
    | Invoices
    |--------------------------------------------------------------------------
    */
    Route::prefix('invoices')->name('invoices.')->group(function () {

        Route::get('/', InvoiceList::class)
            ->name('index');

        Route::get('/create', CreateInvoice::class)
            ->name('create');

        Route::get('/{invoice}', InvoiceShow::class)
            ->name('show');
    });

    Route::get(
        '/export/{format}/{type}/{id}',
        \App\Http\Controllers\ExportController::class
    )->name('export');

});
