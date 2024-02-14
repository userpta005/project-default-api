<?php

use App\Http\Controllers\EnumController;
use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/user-permissions', [PermissionController::class, 'userPermissions']);
    Route::get('/permissions-tree', [PermissionController::class, 'permissionsToTree']);
    Route::get('/enums', [EnumController::class, 'index']);
});
