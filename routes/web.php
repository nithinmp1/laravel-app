<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RosterController;

Route::get('/', function () {
    return ['message' => 'Welcome to web services v1'];
});

Route::get('/getCsrfToken', function () {
    return response()->json([
        'csrfToken' => csrf_token()
    ]);
});

Route::post('/rosters', [RosterController::class, 'index']);
Route::get('/rosters', [RosterController::class, 'getRosters']);
Route::post('/fligts', [RosterController::class, 'getFligts']);
Route::get('/fligts', [RosterController::class, 'getFligtsNextWeek']);
Route::get('/SBYEvents', [RosterController::class, 'getSBYNextWeek']);
Route::post('/roasterFilter', [RosterController::class, 'roasterFilter']);


Route::get('/loadData', [RosterController::class, 'loadFile']);
Route::get('/truncateDatabase', [RosterController::class, 'truncateDatabase']);
