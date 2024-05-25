<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;



Route::group([
    "middleware" => "auth.admin:api",
    'prefix' => "products"
], function () {
    Route::get("/", [ProductController::class, "index"]);
    Route::get("product/{id}", [ProductController::class, "show"]);
    Route::post("/store", [ProductController::class, "store"]);
    Route::post("/update/{id}", [ProductController::class, "update"]);
    Route::post("/delete/{id}", [ProductController::class, "delete"]);
    Route::get("/search", [ProductController::class, "search"]);
});
