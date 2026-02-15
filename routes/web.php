<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoutingController;

// Livewire Components
use App\Livewire\DashboardPanel;

// Users
use App\Livewire\Users\RoleManager;
use App\Livewire\Users\UserManager;

// Settings
use App\Livewire\Settings\ManageSettings;
use App\Livewire\Settings\ManageCurrencies;

// Products
use App\Livewire\Products\ProductForm;
use App\Livewire\Products\ProductList;
use App\Livewire\Products\BrandManager;
use App\Livewire\Products\CategoryManager;
use App\Livewire\Products\UnitManager;

// Purchases
use App\Livewire\Purchases\PurchaseForm;
use App\Livewire\Purchases\PurchaseList;
use App\Livewire\Purchases\PurchaseShow;

// Sales
use App\Livewire\Sales\SaleForm;
use App\Livewire\Sales\SaleList;
use App\Livewire\Sales\SaleShow;

// Sale Returns
use App\Livewire\Sales\ReturnForm;
use App\Livewire\Sales\ReturnList;
use App\Livewire\Sales\ReturnShow;

// Suppliers
use App\Livewire\Suppliers\SupplierForm;
use App\Livewire\Suppliers\SupplierList;

// Customers
use App\Livewire\Customers\CustomerForm;
use App\Livewire\Customers\CustomerList;

// Invoices
use App\Livewire\Invoices\CreateInvoice;
use App\Livewire\Invoices\InvoiceList;
use App\Livewire\Invoices\InvoiceShow;
use App\Livewire\Settings\ManageCompanyBranding;
use App\Livewire\Settings\ManageInvoiceSettings;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';

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


    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])
        ->name('third');

    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])
        ->name('second');

    Route::get('{any}', [RoutingController::class, 'root'])
        ->name('any');
});
