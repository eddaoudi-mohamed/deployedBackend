<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::group([
    "middleware" => "auth.admin:api",
    'prefix' => 'categories'
], function () {
    Route::get('/', [CategoryController::class, "index"]);
    Route::post('/store', [CategoryController::class, "store"]);
    Route::post('/delete/{id}', [CategoryController::class, "delete"]);
});
