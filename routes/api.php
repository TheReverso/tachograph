<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CountryController;
use App\Http\Controllers\API\FreightController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::apiResource('freights', FreightController::class)->middleware('auth:api')->except('show');
Route::apiResource('countries', CountryController::class)->middleware('auth:api')->except('show');
