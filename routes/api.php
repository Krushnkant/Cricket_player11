<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\MatchController;

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

Route::post('login', [AuthController::class, 'login']);
Route::post('verify_otp', [AuthController::class, 'verify_otp']);
Route::post('user_login_log', [AuthController::class, 'user_login_log']);
Route::post('update_token',[AuthController::class,'update_token']);

Route::post('match_commentries',[MatchController::class,'match_commentries']);
Route::post('match_scoreboards',[MatchController::class,'match_scoreboards']);
Route::post('players',[MatchController::class,'players']);
Route::post('match',[MatchController::class,'match']);
Route::post('home',[MatchController::class,'home']);
Route::post('upcoming_series',[MatchController::class,'upcoming_series']);
Route::post('matchlist',[MatchController::class,'matchlist']);
Route::post('otherlist',[MatchController::class,'otherlist']);
Route::post('countrylist',[MatchController::class,'countrylist']);
Route::post('playerlist',[MatchController::class,'playerlist']);
Route::post('teamvsteam',[MatchController::class,'teamvsteam']);
Route::post('playervsplayer',[MatchController::class,'playervsplayer']);
Route::post('playervsteam',[MatchController::class,'playervsteam']);
Route::post('player_profile',[MatchController::class,'player_profile']);
