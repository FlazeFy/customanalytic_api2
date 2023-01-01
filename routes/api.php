<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\APIController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('/aircraft')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [APIController::class, 'getAllAircraft']);
});

Route::prefix('/ships')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [APIController::class, 'getAllShips']);
});

Route::prefix('/vehicles')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [APIController::class, 'getAllVehicles']);
});