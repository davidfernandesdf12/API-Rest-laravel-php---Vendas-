<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('API')->name('api.')->group(function(){
    
    #Region API Product
    Route::prefix('products')->group(function(){
        Route::get('/', 'ProductController@index')->name('index_produts');
        Route::get('/{id}', 'ProductController@show')->name('single_products');

        Route::post('/' , 'ProductController@store')->name('store_products');
        Route::put('/{id}' , 'ProductController@update')->name('update_products');
        Route::delete('/{id}' , 'ProductController@delete')->name('delte_products');

    });
    #endRegion API Product 

    #Region API Custumer
    Route::prefix('custumers')->group(function(){
        Route::get('/', 'CustumerController@index')->name('index_custumers');
        Route::get('/{id}', 'CustumerController@show')->name('single_custumers');

        Route::post('/', 'CustumerController@store')->name('store_custumers');
        Route::put('/{id}', 'CustumerController@update')->name('update_custumers');
        Route::delete('/{id}', 'CustumerController@delete')->name('detele_custumers');
    });
    #endRegion API Custumer

    #Region API Seller
    Route::prefix('sellers')->group(function(){
        Route::get('/', 'SellerController@index')->name('index_sellers');
        Route::get('/{id}', 'SellerController@show')->name('single_sellers');

        Route::post('/', 'SellerController@store')->name('store_sellers');
        Route::put('/{id}', 'SellerController@update')->name('update_sellers');
        Route::delete('/{id}', 'SellerController@delete')->name('delete_sellers');
    });
    #endRegion API Seller

    #Region API Sale
    Route::prefix('sales')->group(function(){
        Route::get('/', 'SaleController@index')->name('index_sales');
        Route::get('/sales_sellers','SaleController@sale_sellers')->name('sales_sellers');  
        
        Route::prefix('sales_sellers')->group(function(){
            Route::get('/', 'SaleController@sale_sellers')->name('sale_sellers');
    
        });


        Route::post('/', 'SaleController@store')->name('store_sales');
        Route::put('/{id}', 'SaleController@update')->name('update_sales');
        Route::delete('/{id}', 'SaleController@delete')->name('delete_sales');

    });

    
    #endRegion APi Sale

});
