<?php

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
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleReturn;

require __DIR__ . '/auth.php';

Route::prefix('product')->name('product.')->group(function () {
    Route::get('/units', UnitManager::class);
    Route::get('/categories', CategoryManager::class);
    Route::get('/brands', BrandManager::class);
});

Route::get('/products',  ProductList::class)->name('products.list');
Route::get('/product/create', ProductForm::class)->name('product.create');
Route::get('/product/{id}/edit', ProductForm::class)->name('product.edit');

Route::get('/purchases', fn() => view('inventory.purchases.purchase-list'))->name('purchase.index');
Route::get('/purchases/create', fn() => view('inventory.purchases.purchase-form'))->name('purchase.create');
Route::get('/purchases/edit/{purchase}', fn($purchase) => view('inventory.purchases.purchase-form', ['purchase' => $purchase]))->name('purchase.edit');

Route::get('/purchases/{purchase}', fn($purchase) => view('inventory.purchases.purchase-show', ['purchase' => $purchase]))->name('purchase.show');


Route::get('/suppliers', fn() => view('inventory.suppliers.supplier-list'))->name('suppliers.index');
Route::get('/suppliers/create', fn() => view('inventory.suppliers.supplier-form'))->name('suppliers.create');
Route::get('/suppliers/edit/{supplier}', fn($supplier) => view('inventory.suppliers.supplier-form', ['supplier' => $supplier]))->name('supplier.edit');

Route::get('/sales', fn() => view('inventory.sales.sale-list'))->name('sales.index');
Route::get('/sales/create', fn() => view('inventory.sales.sale-from'))->name('sales.create');
Route::get('/sales/edit/{sale}', fn($sale) => view('inventory.sales.sale-from', ['sale' => $sale]))->name('sale.edit');
Route::get('/sales/show/{sale}', function ($sale) {
    return view('inventory.sales.sale-show', [
        'sale' => Sale::findOrFail($sale)
    ]);
})->name('sale.show');

Route::get('/sales/returns', fn() => view('inventory.sales.return-list'))->name('sale-returns.index');

Route::get('/sales/returns/create', fn() => view('inventory.sales.return-form'))->name('sale-returns.create');

Route::get('/sales/returns/edit/{return}', fn($return) => view('inventory.sales.return-form', [
    'return' => SaleReturn::findOrFail($return),
]))->name('sale-returns.edit');

Route::get('/sales/returns/show/{return}', fn($return) => view('inventory.sales.return-show', [
    'return' => SaleReturn::findOrFail($return),
]))->name('sale-returns.show');

Route::get('/customers', action: fn() => view('inventory.customers.customer-list'))->name('customer.index');
Route::get('/customers/create', fn() => view('inventory.customers.customer-form', ['customer' => null]))->name('customers.create');
Route::get('/customers/edit/{customer}', function (Customer $customer) {
    return view('inventory.customers.customer-form', ['customer' => $customer]);
})->name('customer.edit');


//Invoices Route
Route::get('/invoices', InvoiceList::class)->name('invoices.index');
Route::get('/invoices/{invoice}', InvoiceShow::class)->name('invoices.show');
Route::get('/invoice/create', CreateInvoice::class)->name('invoices.create');

Route::group(['prefix' => '/', 'middleware' => 'auth'], function () {
    Route::get('', DashboardPanel::class)->name('root');

    // Route::get('/home', fn() => view('index'))->name('home');
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');
});
