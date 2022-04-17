<?php

use App\Http\Controllers\AircompanyController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\SegmentController;
use App\Http\Controllers\PassangerController;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthenticateController;

use App\Http\Middleware\PrivateMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMiddleware;

use Illuminate\Support\Facades\Route;





//Public methods
Route::get('/Ticket/Cities', [TicketController::class, 'cities']);
Route::get('/Ticket/Search/{departure}/{arrival}/{date}', [TicketController::class, 'search']);


Route::post('/Authenticate/IsUser', [AuthenticateController::class, 'isUser']);
Route::post('/Authenticate/IsMaker', [AuthenticateController::class, 'isMaker']);
Route::post('/Authenticate/IsAdmin', [AuthenticateController::class, 'isAdmin']);


//User methods
Route::post('/Passanger/Create', [PassangerController::class, 'create']);
Route::get('/Passanger/SignIn/{email}/{password}', [SignInController::class, 'signInUser']);

Route::middleware([UserMiddleware::class])->group(function () {
    Route::get('/Passanger/Get', [PassangerController::class, 'get']);
    Route::get('/Passanger/Tickets/{id}', [PassangerController::class, 'tickets']);
    Route::post('/Passanger/LoadAvatar/{id}', [PassangerController::class, 'loadAvatar']);
    Route::post('/Passanger/Update/{id}', [PassangerController::class, 'update']);
    Route::post('/Passanger/Delete/{id}', [PassangerController::class, 'delete']);
});



//Protected methods
Route::post('/Booking/Create', [BookingController::class, 'create']);
Route::post('/Booking/Confirm', [BookingController::class, 'confirm']);



//Private methods
Route::get('/Maker/SignIn/{name}/{password}', [SignInController::class, 'signInMaker']);

Route::middleware([PrivateMiddleware::class])->group(function () {
    Route::get('/Admin/Get', [AdminController::class, 'get']);

    Route::get('/Aircompany/All', [AircompanyController::class, 'all']);
    Route::get('/Aircompany/Get/{id}', [AircompanyController::class, 'get']);
    Route::post('/Aircompany/Create', [AircompanyController::class, 'create']);
    Route::post('/Aircompany/LoadLogo/{id}', [AircompanyController::class, 'loadLogo']);
    Route::post('/Aircompany/Update/{id}', [AircompanyController::class, 'update']);
    Route::post('/Aircompany/Delete/{id}', [AircompanyController::class, 'delete']);

    Route::get('/Ticket/All', [TicketController::class, 'all']);
    Route::get('/Ticket/Get/{id}', [TicketController::class, 'get']);
    Route::post('/Ticket/Create', [TicketController::class, 'create']);
    Route::post('/Ticket/LoadPreview/{id}', [TicketController::class, 'loadPreview']);
    Route::post('/Ticket/Update/{id}', [TicketController::class, 'update']);
    Route::post('/Ticket/Delete/{id}', [TicketController::class, 'delete']);

    Route::get('/Segment/All', [SegmentController::class, 'all']);
    Route::get('/Segment/Get/{id}', [SegmentController::class, 'get']);
    Route::post('/Segment/Create', [SegmentController::class, 'create']);
    Route::post('/Segment/Update/{id}', [SegmentController::class, 'update']);
    Route::post('/Segment/Delete/{id}', [SegmentController::class, 'delete']);
});



//Admin methods
Route::get('/Admin/SignIn/{name}/{password}', [SignInController::class, 'signInAdmin']);

Route::middleware([AdminMiddleware::class])->group(function () {
    Route::get('/Admin/AllMakers', [AdminController::class, 'allMakers']);
    Route::get('/Admin/GetMaker/{id}', [AdminController::class, 'getMaker']);
    Route::post('/Admin/CreateMaker', [AdminController::class, 'createMaker']);
    Route::post('/Admin/UpdateMaker/{id}', [AdminController::class, 'updateMaker']);
    Route::post('/Admin/DeleteMaker/{id}', [AdminController::class, 'deleteMaker']);

    Route::get('/Admin/AllPassangers', [AdminController::class, 'allPassangers']);
    Route::get('/Admin/GetPassanger/{id}', [AdminController::class, 'getPassanger']);
    Route::post('/Admin/UpdatePassanger/{id}', [AdminController::class, 'updatePassanger']);
    Route::post('/Admin/DeletePassanger/{id}', [AdminController::class, 'deletePassanger']);
    
    Route::get('/Ticket/Passangers/{id}', [TicketController::class, 'passangers']);
});
