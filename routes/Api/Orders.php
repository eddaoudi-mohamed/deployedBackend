<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;



Route::group([
    "middleware" => "auth.admin:api",
    'prefix' => "orders"
], function () {
    Route::get("/", [OrderController::class, "index"]);
    Route::get("/order/{id}", [OrderController::class, "show"]);
    Route::post("/store", [OrderController::class, "store"]);
    Route::post("/update/{id}", [OrderController::class, "update"]);
    Route::post("/delete/{id}", [OrderController::class, "delete"]);
    Route::get("/search", [OrderController::class, "search"]);
    Route::post("/paid/{id}", [OrderController::class, "paid"]);
    Route::post("/refunded/{id}", [OrderController::class, "refunded"]);
});
