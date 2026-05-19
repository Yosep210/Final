<?php

use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\RolePermissionController;
use App\Http\Controllers\Api\VillageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('countries', CountryController::class);
    Route::apiResource('provinces', ProvinceController::class);
    Route::apiResource('cities', CityController::class);
    Route::apiResource('districts', DistrictController::class);
    Route::apiResource('villages', VillageController::class);

    Route::get('role-permissions', [RolePermissionController::class, 'index']);
    Route::post('role-permissions', [RolePermissionController::class, 'store']);
    Route::get('role-permissions/{role_id}/{permission_id}', [RolePermissionController::class, 'show']);
    Route::match(['put', 'patch'], 'role-permissions/{role_id}/{permission_id}', [RolePermissionController::class, 'update']);
    Route::delete('role-permissions/{role_id}/{permission_id}', [RolePermissionController::class, 'destroy']);
});
