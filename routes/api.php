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
    Route::get('/total/bysides', [APIController::class, 'getTotalAircraftBySides']);
    Route::get('/summary', [APIController::class, 'getAircraftSummary']);
});

Route::prefix('/ships')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [APIController::class, 'getAllShips']);
    Route::get('/total/byclass', [APIController::class, 'getTotalShipsByClass']);
    Route::get('/total/bycountry', [APIController::class, 'getTotalShipsByCountry']);
    Route::get('/total/bysides', [APIController::class, 'getTotalShipsBySides']);
    Route::get('/total/bylaunchyear', [APIController::class, 'getTotalShipsByLaunchYear']);
});

Route::prefix('/vehicles')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [APIController::class, 'getAllVehicles']);
    Route::get('/total/byrole', [APIController::class, 'getTotalVehiclesByRole']);
    Route::get('/total/bycountry', [APIController::class, 'getTotalVehiclesByCountry']);
    Route::get('/total/bysides', [APIController::class, 'getTotalVehiclesBySides']);
    Route::get('/summary', [APIController::class, 'getVehiclesSummary']);
});

Route::prefix('/facilities')->group(function () {
    Route::get('/total/bytype', [APIController::class, 'getTotalFacilitiesByType']);
    Route::get('/total/bycountry', [APIController::class, 'getTotalFacilitiesByCountry']);
    Route::get('/total/bysides', [APIController::class, 'getTotalFacilitiesBySides']);
    Route::get('/bylocation/{type}', [APIController::class, 'getFacilitiesByLocation']);
    Route::get('/type', [APIController::class, 'getFacilitiesType']);
});

Route::prefix('/weapons')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [APIController::class, 'getAllWeapons']);
    Route::get('/total/bytype', [APIController::class, 'getTotalWeaponsByType']);
    Route::get('/total/bycountry', [APIController::class, 'getTotalWeaponsByCountry']);
    Route::get('/total/bysides', [APIController::class, 'getTotalWeaponsBySides']);
    Route::get('/summary', [APIController::class, 'getWeaponsSummary']);
});

Route::prefix('/events')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [APIController::class, 'getAllEvents']);
});

Route::prefix('/books')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [APIController::class, 'getAllBooks']);
    Route::get('/total/byreviewer', [APIController::class, 'getTotalBooksByReviewer']);
    Route::get('/total/byyearreview', [APIController::class, 'getTotalBooksByYearReview']);
});

Route::prefix('/casualities')->group(function () {
    Route::get('/limit/{page_limit}/order/{orderby}/{ordertype}', [APIController::class, 'getAllCasualities']);
    Route::get('/totaldeath/bycountry/{order}/limit/{page_limit}', [APIController::class, 'getTotalDeathByCountry']);
    Route::get('/totaldeath/bysides', [APIController::class, 'getTotalDeathBySides']);
    Route::get('/summary', [APIController::class, 'getCasualitiesSummary']);
});