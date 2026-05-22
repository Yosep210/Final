<?php

use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\VillageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('countries', CountryController::class);
    Route::apiResource('provinces', ProvinceController::class);
    Route::apiResource('cities', CityController::class);
    Route::apiResource('districts', DistrictController::class);
    Route::apiResource('villages', VillageController::class);
    Route::apiResource('members', MemberController::class);
    Route::get('members/{member}/network', [MemberController::class, 'network']);
    Route::post('members/{member}/promote', [MemberController::class, 'promote']);
    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('roles', RoleController::class);
});
