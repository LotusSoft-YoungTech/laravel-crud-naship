<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\Productswebcontroller;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('products.index');
});


Route::resource('products', Productswebcontroller::class);
