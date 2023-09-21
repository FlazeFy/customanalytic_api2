<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AircraftController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\CasualitiesController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\FacilitiesController;
use App\Http\Controllers\ShipsController;
use App\Http\Controllers\VehiclesController;
use App\Http\Controllers\WeaponsController;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);

Route::prefix('/aircraft')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [AircraftController::class, 'getAllAircraft']);
    Route::get('/total/byrole/{limit}', [AircraftController::class, 'getTotalAircraftByRole']);
    Route::get('/total/bycountry/{limit}', [AircraftController::class, 'getTotalAircraftByCountry']);
    Route::get('/total/bysides', [AircraftController::class, 'getTotalAircraftBySides']);
    Route::get('/total/bymanufacturer/{limit}', [AircraftController::class, 'getTotalAircraftByManufacturer']);
    Route::get('/summary', [AircraftController::class, 'getAircraftSummary']);
    Route::delete('/{id}', [AircraftController::class, 'deleteAircraftById']);
});

Route::prefix('/ships')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [ShipsController::class, 'getAllShips']);
    Route::get('/total/byclass', [ShipsController::class, 'getTotalShipsByClass']);
    Route::get('/total/bycountry/{limit}', [ShipsController::class, 'getTotalShipsByCountry']);
    Route::get('/total/bysides', [ShipsController::class, 'getTotalShipsBySides']);
    Route::get('/total/bylaunchyear', [ShipsController::class, 'getTotalShipsByLaunchYear']);
    Route::get('/summary', [ShipsController::class, 'getShipsSummary']);
    Route::delete('/{id}', [ShipsController::class, 'deleteShipById']);
});

Route::prefix('/vehicles')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [VehiclesController::class, 'getAllVehicles']);
    Route::get('/total/byrole/{limit}', [VehiclesController::class, 'getTotalVehiclesByRole']);
    Route::get('/total/bycountry/{limit}', [VehiclesController::class, 'getTotalVehiclesByCountry']);
    Route::get('/total/bysides', [VehiclesController::class, 'getTotalVehiclesBySides']);
    Route::get('/summary', [VehiclesController::class, 'getVehiclesSummary']);
    Route::delete('/{id}', [VehiclesController::class, 'deleteVechilesById']);
});

Route::prefix('/facilities')->group(function () {
    Route::get('/total/bytype/{limit}', [FacilitiesController::class, 'getTotalFacilitiesByType']);
    Route::get('/total/bycountry/{limit}', [FacilitiesController::class, 'getTotalFacilitiesByCountry']);
    Route::get('/total/bysides', [FacilitiesController::class, 'getTotalFacilitiesBySides']);
    Route::get('/bylocation/{type}', [FacilitiesController::class, 'getFacilitiesByLocation']);
    Route::get('/type', [FacilitiesController::class, 'getFacilitiesType']);
    Route::get('/summary', [FacilitiesController::class, 'getFacilitiesSummary']);
});

Route::prefix('/weapons')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [WeaponsController::class, 'getAllWeapons']);
    Route::get('/total/bytype/{limit}', [WeaponsController::class, 'getTotalWeaponsByType']);
    Route::get('/total/bycountry/{limit}', [WeaponsController::class, 'getTotalWeaponsByCountry']);
    Route::get('/total/bysides', [WeaponsController::class, 'getTotalWeaponsBySides']);
    Route::get('/summary', [WeaponsController::class, 'getWeaponsSummary']);
    Route::delete('/{id}', [WeaponsController::class, 'deleteWeaponById']);
});

Route::prefix('/events')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [EventsController::class, 'getAllEvents']);
    Route::delete('/{id}', [EventsController::class, 'deleteEventById']);
});

Route::prefix('/books')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [BooksController::class, 'getAllBooks']);
    Route::get('/total/byreviewer/{limit}', [BooksController::class, 'getTotalBooksByReviewer']);
    Route::get('/total/byyearreview', [BooksController::class, 'getTotalBooksByYearReview']);
    Route::delete('/{id}', [BooksController::class, 'deleteBookById']);
});

Route::prefix('/casualities')->group(function () {
    Route::get('/limit/{page_limit}/order/{orderby}/{ordertype}', [CasualitiesController::class, 'getAllCasualities']);
    Route::get('/totaldeath/bycountry/{order}/limit/{page_limit}', [CasualitiesController::class, 'getTotalDeathByCountry']);
    Route::get('/totaldeath/bysides/{view}', [CasualitiesController::class, 'getTotalDeathBySides']);
    Route::get('/summary', [CasualitiesController::class, 'getCasualitiesSummary']);
});