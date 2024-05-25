<?php

use App\Http\Controllers\AuthAdmin\AuthAdmin;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('/login', [AuthAdmin::class, 'login']);
    Route::post('/logout', [AuthAdmin::class, 'logout']);
    Route::get('/refresh', [AuthAdmin::class, 'refresh'])->name("api.refresh");
});
