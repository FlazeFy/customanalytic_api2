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
    Route::get('/total/byrole', [APIController::class, 'getTotalAircraftByRole']);
    Route::get('/total/bycountry', [APIController::class, 'getTotalAircraftByCountry']);
});

Route::prefix('/ships')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [APIController::class, 'getAllShips']);
    Route::get('/total/byclass', [APIController::class, 'getTotalShipsByClass']);
    Route::get('/total/bycountry', [APIController::class, 'getTotalShipsByCountry']);
});

Route::prefix('/vehicles')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [APIController::class, 'getAllVehicles']);
    Route::get('/total/byrole', [APIController::class, 'getTotalVehiclesByRole']);
    Route::get('/total/bycountry', [APIController::class, 'getTotalVehiclesByCountry']);
});

Route::prefix('/facilities')->group(function () {
    Route::get('/total/bytype', [APIController::class, 'getTotalFacilitiesByType']);
    Route::get('/total/bycountry', [APIController::class, 'getTotalFacilitiesByCountry']);
});

Route::prefix('/weapons')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [APIController::class, 'getAllWeapons']);
    Route::get('/total/bytype', [APIController::class, 'getTotalWeaponsByType']);
});

Route::prefix('/events')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [APIController::class, 'getAllEvents']);
});

Route::prefix('/books')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [APIController::class, 'getAllBooks']);
    Route::get('/total/byreviewer', [APIController::class, 'getTotalBooksByReviewer']);
});

Route::prefix('/casualities')->group(function () {
    Route::get('/limit/{page_limit}/order/{orderby}/{ordertype}', [APIController::class, 'getAllCasualities']);
});