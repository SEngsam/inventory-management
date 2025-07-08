<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoutingController;
use App\Livewire\ProductForm;
use App\Livewire\ProductList;
use App\Models\Customer;
use App\Models\Sale;

require __DIR__ . '/auth.php';

Route::prefix('product')->name('product.')->group(function () {
    Route::get('/units', function () {return view('inventory.units');});
    Route::get('/categories', function () {return view('inventory.categories');});
    Route::get('/brands', function () {return view('inventory.brands');});
});

Route::get('/product',  fn() => view('inventory.product.list'))->name('product.list');
Route::get('/product/create', fn() => view('inventory.product.form'))->name('product.create');


Route::get('/products/{id}/edit', fn($id) => view('inventory.product.form', ['id' => $id]))->name('product.edit');
Route::get('/purchases', fn() => view('inventory.purchases.purchase-list'))->name('purchases.list');
Route::get('/purchases/create', fn() => view('inventory.purchases.purchase-form'))->name('purchases.create');
Route::get('/purchases/{purchaseId}', fn() => view('inventory.purchases.purchase-show'))->name('purchases.show');


Route::get('/suppliers', fn() => view('inventory.suppliers.supplier-list'))->name('suppliers.index');
Route::get('/suppliers/create', fn() => view('inventory.suppliers.supplier-form'))->name('suppliers.create');
Route::get('/suppliers/{supplierId}/edit', fn() => view('inventory.suppliers.supplier-form'))->name('suppliers.edit');

Route::get('/sales', fn() => view('inventory.sales.sale-list'))->name('sales.index');
Route::get('/sales/create', fn() => view('inventory.sales.sale-from'))->name('sales.create');
Route::get('/sales/edit/{sale}', fn($sale) => view('inventory.sales.sale-from', ['sale' => $sale]))->name('sale.edit');
Route::get('/sales/show/{sale}', function ($sale) {
    return view('inventory.sales.sale-show', [
        'sale' => Sale::findOrFail($sale)
    ]);
})->name('sale.show');

Route::get('/customers', action: fn() => view('inventory.customers.customer-list'))->name('customer.index');
Route::get('/customers/create', fn() => view('inventory.customers.customer-form', ['customer' => null]))->name('customers.create');
Route::get('/customers/edit/{customer}', function (Customer $customer) {
    return view('inventory.customers.customer-form', ['customer' => $customer]);
})->name('customer.edit');

Route::group(['prefix' => '/', 'middleware' => 'auth'], function () {
    Route::get('', [RoutingController::class, 'index'])->name('root');
    Route::get('/home', fn() => view('index'))->name('home');
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');
});
