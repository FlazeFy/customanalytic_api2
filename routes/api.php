<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AircraftController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\CasualitiesController;
use App\Http\Controllers\DiscussionsController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\FacilitiesController;
use App\Http\Controllers\FeedbacksController;
use App\Http\Controllers\ShipsController;
use App\Http\Controllers\VehiclesController;
use App\Http\Controllers\WeaponsController;
use App\Http\Controllers\StoriesController;
use Spatie\LaravelIgnition\Solutions\LivewireDiscoverSolution;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);

Route::prefix('/aircraft')->group(function () {
    Route::post('/', [AircraftController::class, 'createAircraft']);
    Route::get('/limit/{page_limit}/order/{order}/find/{search}', [AircraftController::class, 'getAllAircraft']);
    Route::get('/total/byrole/{limit}', [AircraftController::class, 'getTotalAircraftByRole']);
    Route::get('/total/bycountry/{limit}', [AircraftController::class, 'getTotalAircraftByCountry']);
    Route::get('/total/bysides', [AircraftController::class, 'getTotalAircraftBySides']);
    Route::get('/total/bymanufacturer/{limit}', [AircraftController::class, 'getTotalAircraftByManufacturer']);
    Route::get('/summary', [AircraftController::class, 'getAircraftSummary']);
    Route::delete('/{id}', [AircraftController::class, 'deleteAircraftById']);
    Route::put('/{id}', [AircraftController::class, 'updateAircraftById']);
});

Route::prefix('/ships')->group(function () {
    Route::post('/', [ShipsController::class, 'createShip']);
    Route::get('/limit/{page_limit}/order/{order}/find/{search}', [ShipsController::class, 'getAllShips']);
    Route::get('/total/byclass', [ShipsController::class, 'getTotalShipsByClass']);
    Route::get('/total/bycountry/{limit}', [ShipsController::class, 'getTotalShipsByCountry']);
    Route::get('/total/bysides', [ShipsController::class, 'getTotalShipsBySides']);
    Route::get('/total/bylaunchyear', [ShipsController::class, 'getTotalShipsByLaunchYear']);
    Route::get('/summary', [ShipsController::class, 'getShipsSummary']);
    Route::delete('/{id}', [ShipsController::class, 'deleteShipById']);
    Route::put('/{id}', [ShipsController::class, 'updateShipById']);
});

Route::prefix('/vehicles')->group(function () {
    Route::post('/', [VehiclesController::class, 'createVehicles']);
    Route::get('/limit/{page_limit}/order/{order}/find/{search}', [VehiclesController::class, 'getAllVehicles']);
    Route::get('/total/byrole/{limit}', [VehiclesController::class, 'getTotalVehiclesByRole']);
    Route::get('/total/bycountry/{limit}', [VehiclesController::class, 'getTotalVehiclesByCountry']);
    Route::get('/total/bysides', [VehiclesController::class, 'getTotalVehiclesBySides']);
    Route::get('/summary', [VehiclesController::class, 'getVehiclesSummary']);
    Route::delete('/{id}', [VehiclesController::class, 'deleteVehiclesById']);
    Route::put('/{id}', [VehiclesController::class, 'updateVehicleById']);
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
    Route::post('/', [WeaponsController::class, 'createWeapon']);
    Route::get('/limit/{page_limit}/order/{order}/find/{search}', [WeaponsController::class, 'getAllWeapons']);
    Route::get('/total/bytype/{limit}', [WeaponsController::class, 'getTotalWeaponsByType']);
    Route::get('/total/bycountry/{limit}', [WeaponsController::class, 'getTotalWeaponsByCountry']);
    Route::get('/total/bysides', [WeaponsController::class, 'getTotalWeaponsBySides']);
    Route::get('/summary', [WeaponsController::class, 'getWeaponsSummary']);
    Route::delete('/{id}', [WeaponsController::class, 'deleteWeaponById']);
    Route::put('/{id}', [WeaponsController::class, 'updateWeaponById']);
});

Route::prefix('/events')->group(function () {
    Route::post('/', [EventsController::class, 'createEvent']);
    Route::get('/limit/{page_limit}/order/{order}', [EventsController::class, 'getAllEvents']);
    Route::delete('/{id}', [EventsController::class, 'deleteEventById']);
    Route::put('/{id}', [EventsController::class, 'updateEventById']);
});

Route::prefix('/books')->group(function () {
    Route::post('/', [BooksController::class, 'createBook']);
    Route::get('/limit/{page_limit}/order/{order}/find/{search}', [BooksController::class, 'getAllBooks']);
    Route::get('/total/byreviewer/{limit}', [BooksController::class, 'getTotalBooksByReviewer']);
    Route::get('/total/byyearreview', [BooksController::class, 'getTotalBooksByYearReview']);
    Route::delete('/{id}', [BooksController::class, 'deleteBookById']);
    Route::put('/{id}', [BooksController::class, 'updateBookById']);
});

Route::prefix('/casualities')->group(function () {
    Route::get('/limit/{page_limit}/order/{orderby}/{ordertype}', [CasualitiesController::class, 'getAllCasualities']);
    Route::get('/totaldeath/bycountry/{order}/limit/{page_limit}', [CasualitiesController::class, 'getTotalDeathByCountry']);
    Route::get('/totaldeath/bysides/{view}', [CasualitiesController::class, 'getTotalDeathBySides']);
    Route::get('/summary', [CasualitiesController::class, 'getCasualitiesSummary']);
});

Route::prefix('/discussions')->group(function () {
    Route::post('/', [DiscussionsController::class, 'createDiscussion']);
    Route::get('/limit/{page_limit}/order/{order}', [DiscussionsController::class, 'getAllDiscussions']); // belum implement
});

Route::prefix('/feedbacks')->group(function () {
    Route::post('/', [FeedbacksController::class, 'createFeedback']);
    Route::get('/limit/{page_limit}/order/{order}', [FeedbacksController::class, 'getAllFeedback']);
});

Route::prefix('/stories')->group(function () {
    Route::get('/limit/{page_limit}/order/{order}', [StoriesController::class, 'getAllStories']);
    Route::get('/detail/{slug}', [StoriesController::class, 'getStoriesBySlug']);
    Route::get('/type/{type}/creator/{creator}', [StoriesController::class, 'getSimiliarStories']);
});