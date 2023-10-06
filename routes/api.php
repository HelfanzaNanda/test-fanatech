<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'LoginController@login');
Route::post('logout', 'LoginController@logout');
Route::post('generate/number', 'UtilsController@generateNumber');

Route::middleware("auth:sanctum")->group(function() {
    Route::prefix('inventory')->group(function(){
        Route::post('datatables',  'InventoryController@datatables')->middleware('role:SUPERADMIN');
        Route::post('options',  'InventoryController@options');
        Route::post('',  'InventoryController@store')->middleware('role:SUPERADMIN');
        Route::get('{id}',  'InventoryController@find')->middleware('role:SUPERADMIN');
        Route::put('{id}',  'InventoryController@update')->middleware('role:SUPERADMIN');
        Route::delete('{id}',  'InventoryController@delete')->middleware('role:SUPERADMIN');
    });

    Route::middleware('role:SUPERADMIN|SALES|MANAGER')->prefix('sales')->group(function(){
        Route::post('datatables',  'SalesController@datatables');
        Route::post('',  'SalesController@store')->middleware('role:SUPERADMIN|SALES');
        Route::get('{id}',  'SalesController@find')->middleware('role:SUPERADMIN|SALES');
        Route::put('{id}',  'SalesController@update')->middleware('role:SUPERADMIN|SALES');
        Route::delete('{id}',  'SalesController@delete')->middleware('role:SUPERADMIN|SALES');
    });

    Route::middleware('role:SUPERADMIN|PURCHASE|MANAGER')->prefix('purchase')->group(function(){
        Route::post('datatables',  'PurchaseController@datatables');
        Route::post('',  'PurchaseController@store')->middleware('role:SUPERADMIN|PURCHASE');
        Route::get('{id}',  'PurchaseController@find')->middleware('role:SUPERADMIN|PURCHASE');
        Route::put('{id}',  'PurchaseController@update')->middleware('role:SUPERADMIN|PURCHASE');
        Route::delete('{id}',  'PurchaseController@delete')->middleware('role:SUPERADMIN|PURCHASE');
    });
});

