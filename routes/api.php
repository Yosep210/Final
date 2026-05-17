<?php

use App\Http\Controllers\Api\CountryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('countries', CountryController::class);
    Route::apiResource('provinces', ProvinceController::class);
});
