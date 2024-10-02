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
    Route::get('/limit/{limit}/order/{order}/find/{search}', [AircraftController::class, 'getAllAircraft']);
    Route::get('/total/byrole/{limit}', [AircraftController::class, 'getTotalAircraftByRole']);
    Route::get('/total/bycountry/{limit}', [AircraftController::class, 'getTotalAircraftByCountry']);
    Route::get('/total/bysides', [AircraftController::class, 'getTotalAircraftBySides']);
    Route::get('/total/bymanufacturer/{limit}', [AircraftController::class, 'getTotalAircraftByManufacturer']);
    Route::get('/', [AircraftController::class, 'getAircraftModule']);
    Route::get('/summary', [AircraftController::class, 'getAircraftSummary']);

    Route::post('/', [AircraftController::class, 'createAircraft'])->middleware(['auth:sanctum']);
    Route::delete('/{id}', [AircraftController::class, 'deleteAircraftById'])->middleware(['auth:sanctum']);
    Route::put('/{id}', [AircraftController::class, 'updateAircraftById'])->middleware(['auth:sanctum']);
});

Route::prefix('/ships')->group(function () {
    Route::get('/limit/{limit}/order/{order}/find/{search}', [ShipsController::class, 'getAllShips']);
    Route::get('/total/byclass/{limit}', [ShipsController::class, 'getTotalShipsByClass']);
    Route::get('/total/bycountry/{limit}', [ShipsController::class, 'getTotalShipsByCountry']);
    Route::get('/total/bysides', [ShipsController::class, 'getTotalShipsBySides']);
    Route::get('/total/bylaunchyear', [ShipsController::class, 'getTotalShipsByLaunchYear']);
    Route::get('/summary', [ShipsController::class, 'getShipsSummary']);
    Route::get('/', [ShipsController::class, 'getShipsModule']);

    Route::post('/', [ShipsController::class, 'createShip'])->middleware(['auth:sanctum']);
    Route::delete('/{id}', [ShipsController::class, 'deleteShipById'])->middleware(['auth:sanctum']);
    Route::put('/{id}', [ShipsController::class, 'updateShipById'])->middleware(['auth:sanctum']);
});

Route::prefix('/vehicles')->group(function () {    
    Route::get('/limit/{limit}/order/{order}/find/{search}', [VehiclesController::class, 'getAllVehicles']);
    Route::get('/total/byrole/{limit}', [VehiclesController::class, 'getTotalVehiclesByRole']);
    Route::get('/total/bycountry/{limit}', [VehiclesController::class, 'getTotalVehiclesByCountry']);
    Route::get('/total/bysides', [VehiclesController::class, 'getTotalVehiclesBySides']);
    Route::get('/summary', [VehiclesController::class, 'getVehiclesSummary']);
    Route::get('/', [VehiclesController::class, 'getVehiclesModule']);

    Route::post('/', [VehiclesController::class, 'createVehicles'])->middleware(['auth:sanctum']);
    Route::delete('/{id}', [VehiclesController::class, 'deleteVehiclesById'])->middleware(['auth:sanctum']);
    Route::put('/{id}', [VehiclesController::class, 'updateVehicleById'])->middleware(['auth:sanctum']);
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
    Route::get('/limit/{limit}/order/{order}/find/{search}', [WeaponsController::class, 'getAllWeapons']);
    Route::get('/total/bytype/{limit}', [WeaponsController::class, 'getTotalWeaponsByType']);
    Route::get('/total/bycountry/{limit}', [WeaponsController::class, 'getTotalWeaponsByCountry']);
    Route::get('/total/bysides', [WeaponsController::class, 'getTotalWeaponsBySides']);
    Route::get('/summary', [WeaponsController::class, 'getWeaponsSummary']);
    Route::get('/', [WeaponsController::class, 'getWeaponsModule']);

    Route::post('/', [WeaponsController::class, 'createWeapon'])->middleware(['auth:sanctum']);
    Route::delete('/{id}', [WeaponsController::class, 'deleteWeaponById'])->middleware(['auth:sanctum']);
    Route::put('/{id}', [WeaponsController::class, 'updateWeaponById'])->middleware(['auth:sanctum']);
});

Route::prefix('/events')->group(function () {
    Route::get('/limit/{limit}/order/{order}', [EventsController::class, 'getAllEvents']);

    Route::post('/', [EventsController::class, 'createEvent'])->middleware(['auth:sanctum']);
    Route::delete('/{id}', [EventsController::class, 'deleteEventById'])->middleware(['auth:sanctum']);
    Route::put('/{id}', [EventsController::class, 'updateEventById'])->middleware(['auth:sanctum']);
});

Route::prefix('/books')->group(function () {
    Route::get('/limit/{limit}/order/{order}/find/{search}', [BooksController::class, 'getAllBooks']);
    Route::get('/total/byreviewer/{limit}', [BooksController::class, 'getTotalBooksByReviewer']);
    Route::get('/total/byyearreview', [BooksController::class, 'getTotalBooksByYearReview']);
    Route::get('/', [BooksController::class, 'getBooksModule']);

    Route::post('/', [BooksController::class, 'createBook'])->middleware(['auth:sanctum']);
    Route::delete('/{id}', [BooksController::class, 'deleteBookById'])->middleware(['auth:sanctum']);
    Route::put('/{id}', [BooksController::class, 'updateBookById'])->middleware(['auth:sanctum']);
});

Route::prefix('/casualities')->group(function () {
    Route::get('/limit/{limit}/order/{orderby}/{ordertype}', [CasualitiesController::class, 'getAllCasualities']);
    Route::get('/totaldeath/bycountry/{order}/limit/{limit}', [CasualitiesController::class, 'getTotalDeathByCountry']);
    Route::get('/totaldeath/bysides/{view}', [CasualitiesController::class, 'getTotalDeathBySides']);
    Route::get('/summary', [CasualitiesController::class, 'getCasualitiesSummary']);
});

Route::prefix('/discussions')->group(function () {
    Route::get('/limit/{limit}/order/{order}/{id}', [DiscussionsController::class, 'getAllDiscussion']); 

    Route::post('/', [DiscussionsController::class, 'createDiscussion'])->middleware(['auth:sanctum']);
});

Route::prefix('/feedbacks')->group(function () {
    Route::get('/limit/{limit}/order/{order}/{id}', [FeedbacksController::class, 'getAllFeedback']);
    Route::get('/stats/{id}', [FeedbacksController::class, 'getStoriesFeedbackStats']);

    Route::post('/', [FeedbacksController::class, 'createFeedback'])->middleware(['auth:sanctum']);
});

Route::prefix('/stories')->group(function () {
    Route::get('/limit/{limit}/order/{order}', [StoriesController::class, 'getAllStories']);
    Route::get('/detail/{slug}', [StoriesController::class, 'getStoriesBySlug']);
    Route::get('/type/{type}/creator/{creator}', [StoriesController::class, 'getSimiliarStories']);
    Route::get('/top/discuss', [StoriesController::class, 'getMostDiscussStories']);
    Route::get('/top/rate', [StoriesController::class, 'getBestRatedStories']);
});

Route::prefix('/user')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/my', [AuthController::class, 'getMyProfile']);
});