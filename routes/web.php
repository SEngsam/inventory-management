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
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', DashboardPanel::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */
    Route::get('/users', UserManager::class)
        ->middleware('permission:users.view')
        ->name('users.index');

    Route::get('/users/roles', RoleManager::class)
        ->middleware('permission:users.roles.manage')
        ->name('users.roles');

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->name('settings.')->group(function () {

        Route::get('/', ManageSettings::class)
            ->middleware('permission:settings.view')
            ->name('general');

        Route::get('/currencies', ManageCurrencies::class)
            ->middleware('permission:settings.currencies.manage')
            ->name('currencies');
    });

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */
    Route::prefix('products')->name('products.')->group(function () {

        Route::get('/', ProductList::class)
            ->middleware('permission:products.view')
            ->name('list');

        Route::get('/create', ProductForm::class)
            ->middleware('permission:products.create')
            ->name('create');

        Route::get('/edit/{product}', ProductForm::class)
            ->middleware('permission:products.update')
            ->name('edit');

        Route::get('/units', UnitManager::class)
            ->middleware('permission:products.units.manage')
            ->name('units');

        Route::get('/categories', CategoryManager::class)
            ->middleware('permission:products.categories.manage')
            ->name('categories');

        Route::get('/brands', BrandManager::class)
            ->middleware('permission:products.brands.manage')
            ->name('brands');
    });

    /*
    |--------------------------------------------------------------------------
    | Purchases
    |--------------------------------------------------------------------------
    */
    Route::prefix('purchases')->name('purchases.')->group(function () {

        Route::get('/', PurchaseList::class)
            ->middleware('permission:purchases.view')
            ->name('index');

        Route::get('/create', PurchaseForm::class)
            ->middleware('permission:purchases.create')
            ->name('create');

        Route::get('/edit/{purchaseId}', PurchaseForm::class)
            ->middleware('permission:purchases.update')
            ->name('edit');

        Route::get('/{purchaseId}', PurchaseShow::class)
            ->middleware('permission:purchases.show')
            ->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Sales
    |--------------------------------------------------------------------------
    */
    Route::prefix('sales')->name('sales.')->group(function () {

        Route::get('/', SaleList::class)
            ->middleware('permission:sales.view')
            ->name('index');

        Route::get('/create', SaleForm::class)
            ->middleware('permission:sales.create')
            ->name('create');

        Route::get('/edit/{id}', SaleForm::class)
            ->middleware('permission:sales.update')
            ->name('edit');

        Route::get('/{id}', SaleShow::class)
            ->middleware('permission:sales.show')
            ->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Sale Returns
    |--------------------------------------------------------------------------
    */
    Route::prefix('returns')->name('sale-returns.')->group(function () {

        Route::get('/', ReturnList::class)
            ->middleware('permission:sale-returns.view')
            ->name('index');

        Route::get('/create', ReturnForm::class)
            ->middleware('permission:sale-returns.create')
            ->name('create');

        Route::get('/edit/{return}', ReturnForm::class)
            ->middleware('permission:sale-returns.update')
            ->name('edit');

        Route::get('/{return}', ReturnShow::class)
            ->middleware('permission:sale-returns.show')
            ->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Suppliers
    |--------------------------------------------------------------------------
    */
    Route::prefix('suppliers')->name('suppliers.')->group(function () {

        Route::get('/', SupplierList::class)
            ->middleware('permission:suppliers.view')
            ->name('index');

        Route::get('/create', SupplierForm::class)
            ->middleware('permission:suppliers.create')
            ->name('create');

        Route::get('/edit/{supplier}', SupplierForm::class)
            ->middleware('permission:suppliers.update')
            ->name('edit');
    });

    /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    */
    Route::prefix('customers')->name('customers.')->group(function () {

        Route::get('/', CustomerList::class)
            ->middleware('permission:customers.view')
            ->name('index');

        Route::get('/create', CustomerForm::class)
            ->middleware('permission:customers.create')
            ->name('create');

        Route::get('/edit/{customer}', CustomerForm::class)
            ->middleware('permission:customers.update')
            ->name('edit');
    });

    /*
    |--------------------------------------------------------------------------
    | Invoices
    |--------------------------------------------------------------------------
    */
    Route::prefix('invoices')->name('invoices.')->group(function () {

        Route::get('/', InvoiceList::class)
            ->middleware('permission:invoices.view')
            ->name('index');

        Route::get('/create', CreateInvoice::class)
            ->middleware('permission:invoices.create')
            ->name('create');

        Route::get('/{invoice}', InvoiceShow::class)
            ->middleware('permission:invoices.show')
            ->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Catch-All 
    |--------------------------------------------------------------------------
    */
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])
        ->middleware('permission:dashboard.view')
        ->name('third');

    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])
        ->middleware('permission:dashboard.view')
        ->name('second');

    Route::get('{any}', [RoutingController::class, 'root'])
        ->middleware('permission:dashboard.view')
        ->name('any');
});