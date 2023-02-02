<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//use App\Http\Controllers\APIController;
use App\Http\Controllers\AircraftController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\CasualitiesController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\FacilitiesController;
use App\Http\Controllers\ShipsController;
use App\Http\Controllers\VehiclesController;
use App\Http\Controllers\WeaponsController;

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
    Route::get('/limit/{page_limit}/order/{order}', [AircraftController::class, 'getAllAircraft']);
    Route::get('/total/byrole', [AircraftController::class, 'getTotalAircraftByRole']);
    Route::get('/total/bycountry', [AircraftController::class, 'getTotalAircraftByCountry']);
    Route::get('/total/bysides', [AircraftController::class, 'getTotalAircraftBySides']);
    Route::get('/total/bymanufacturer/{limit}', [AircraftController::class, 'getTotalAircraftByManufacturer']);
    Route::get('/summary', [AircraftController::class, 'getAircraftSummary']);
});

Route::prefix('/ships')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [ShipsController::class, 'getAllShips']);
    Route::get('/total/byclass', [ShipsController::class, 'getTotalShipsByClass']);
    Route::get('/total/bycountry', [ShipsController::class, 'getTotalShipsByCountry']);
    Route::get('/total/bysides', [ShipsController::class, 'getTotalShipsBySides']);
    Route::get('/total/bylaunchyear', [ShipsController::class, 'getTotalShipsByLaunchYear']);
    Route::get('/summary', [ShipsController::class, 'getShipsSummary']);
});

Route::prefix('/vehicles')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [VehiclesController::class, 'getAllVehicles']);
    Route::get('/total/byrole', [VehiclesController::class, 'getTotalVehiclesByRole']);
    Route::get('/total/bycountry', [VehiclesController::class, 'getTotalVehiclesByCountry']);
    Route::get('/total/bysides', [VehiclesController::class, 'getTotalVehiclesBySides']);
    Route::get('/summary', [VehiclesController::class, 'getVehiclesSummary']);
});

Route::prefix('/facilities')->group(function () {
    Route::get('/total/bytype', [FacilitiesController::class, 'getTotalFacilitiesByType']);
    Route::get('/total/bycountry', [FacilitiesController::class, 'getTotalFacilitiesByCountry']);
    Route::get('/total/bysides', [FacilitiesController::class, 'getTotalFacilitiesBySides']);
    Route::get('/bylocation/{type}', [FacilitiesController::class, 'getFacilitiesByLocation']);
    Route::get('/type', [FacilitiesController::class, 'getFacilitiesType']);
    Route::get('/summary', [FacilitiesController::class, 'getFacilitiesSummary']);
});

Route::prefix('/weapons')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [WeaponsController::class, 'getAllWeapons']);
    Route::get('/total/bytype', [WeaponsController::class, 'getTotalWeaponsByType']);
    Route::get('/total/bycountry', [WeaponsController::class, 'getTotalWeaponsByCountry']);
    Route::get('/total/bysides', [WeaponsController::class, 'getTotalWeaponsBySides']);
    Route::get('/summary', [WeaponsController::class, 'getWeaponsSummary']);
});

Route::prefix('/events')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [EventsController::class, 'getAllEvents']);
});

Route::prefix('/books')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [BooksController::class, 'getAllBooks']);
    Route::get('/total/byreviewer', [BooksController::class, 'getTotalBooksByReviewer']);
    Route::get('/total/byyearreview', [BooksController::class, 'getTotalBooksByYearReview']);
});

Route::prefix('/casualities')->group(function () {
    Route::get('/limit/{page_limit}/order/{orderby}/{ordertype}', [CasualitiesController::class, 'getAllCasualities']);
    Route::get('/totaldeath/bycountry/{order}/limit/{page_limit}', [CasualitiesController::class, 'getTotalDeathByCountry']);
    Route::get('/totaldeath/bysides', [CasualitiesController::class, 'getTotalDeathBySides']);
    Route::get('/summary', [CasualitiesController::class, 'getCasualitiesSummary']);
});