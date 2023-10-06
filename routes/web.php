<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return view('login.index');
// });

// Route::get('/clear', function() {
//     Artisan::call('config:cache');
//      Artisan::call('cache:clear');
//      Artisan::call('config:clear');
//      Artisan::call('view:clear');
//      Artisan::call('route:clear');
//      exec('rm -f ' . storage_path('logs/.log'));
//      exec('rm -f ' . base_path('.log'));
//      return "Cache is cleared";
// })->name('clear.cache');

Route::get('login',  'LoginController@index')->middleware('guest')->name('login');



Route::middleware('auth:sanctum')->group(function(){
    Route::get('',  'DashboardController@index')->name('dashboard.index');
    Route::middleware('role:SUPERADMIN|SALES|MANAGER')->prefix('sales')->group( function(){
        Route::get('',  'SalesController@index')->name('sales.index');
        Route::get('create',  'SalesController@create')->name('sales.create');
        Route::get('{id}/edit',  'SalesController@edit')->name('sales.edit');
        Route::get('{id}',  'SalesController@detail')->name('sales.detail');
    });

    Route::middleware('role:SUPERADMIN|PURCHASE|MANAGER')->prefix('purchase')->group(function(){
        Route::get('',  'PurchaseController@index')->name('purchase.index');
        Route::get('create',  'PurchaseController@create')->name('purchase.create');
        Route::get('{id}/edit',  'PurchaseController@edit')->name('purchase.edit');
        Route::get('{id}',  'PurchaseController@detail')->name('purchase.detail');
    });

    Route::middleware('role:SUPERADMIN')->prefix('inventory')->group(function(){
        Route::get('',  'InventoryController@index')->name('inventory.index');
        Route::get('create',  'InventoryController@create')->name('inventory.create');
        Route::get('{id}/edit',  'InventoryController@edit')->name('inventory.edit');
        Route::get('{id}',  'InventoryController@detail')->name('inventory.detail');
    });
});

