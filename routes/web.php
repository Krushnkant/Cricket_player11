<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('admin',[\App\Http\Controllers\admin\AuthController::class,'index'])->name('admin.login');
Route::post('adminpostlogin', [\App\Http\Controllers\admin\AuthController::class, 'postLogin'])->name('admin.postlogin');
Route::get('logout', [\App\Http\Controllers\admin\AuthController::class, 'logout'])->name('admin.logout');
Route::get('admin/403_page',[\App\Http\Controllers\admin\AuthController::class,'invalid_page'])->name('admin.403_page');

Route::group(['prefix'=>'admin','middleware'=>['auth','userpermission'],'as'=>'admin.'],function () {
        Route::get('dashboard', [\App\Http\Controllers\admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('users',[\App\Http\Controllers\admin\UserController::class,'index'])->name('users.list');
        Route::post('addorupdateuser',[\App\Http\Controllers\admin\UserController::class,'addorupdateuser'])->name('users.addorupdate');
        Route::post('alluserslist',[\App\Http\Controllers\admin\UserController::class,'alluserslist'])->name('alluserslist');
        Route::get('changeuserstatus/{id}',[\App\Http\Controllers\admin\UserController::class,'changeuserstatus'])->name('users.changeuserstatus');
        Route::get('users/{id}/edit',[\App\Http\Controllers\admin\UserController::class,'edituser'])->name('users.edit');
        Route::get('users/{id}/delete',[\App\Http\Controllers\admin\UserController::class,'deleteuser'])->name('users.delete');
        Route::get('users/{id}/permission',[\App\Http\Controllers\admin\UserController::class,'permissionuser'])->name('users.permission');
        Route::post('savepermission',[\App\Http\Controllers\admin\UserController::class,'savepermission'])->name('users.savepermission');

        Route::get('settings',[\App\Http\Controllers\admin\SettingsController::class,'index'])->name('settings.list');
        Route::post('updateInvoiceSetting',[\App\Http\Controllers\admin\SettingsController::class,'updateInvoiceSetting'])->name('settings.updateInvoiceSetting');
        Route::get('settings/edit',[\App\Http\Controllers\admin\SettingsController::class,'editSettings'])->name('settings.edit');

        Route::get('match',[\App\Http\Controllers\admin\MatchController::class,'index'])->name('match.list');
        Route::get('match/create',[\App\Http\Controllers\admin\MatchController::class,'create'])->name('match.add');
        Route::post('match/save',[\App\Http\Controllers\admin\MatchController::class,'save'])->name('match.save');
        Route::post('allmatchlist',[\App\Http\Controllers\admin\MatchController::class,'allmatchlist'])->name('allmatchlist');
        Route::get('match/{id}/edit',[\App\Http\Controllers\admin\MatchController::class,'editmatch'])->name('match.edit');
        Route::get('match/{id}/delete',[\App\Http\Controllers\admin\MatchController::class,'deletematch'])->name('match.delete');

        Route::post('matchplayer/save',[\App\Http\Controllers\admin\MatchController::class,'savematchplayer'])->name('matchplayer.save');

        Route::get('matchplayer/{id}',[\App\Http\Controllers\admin\MatchController::class,'matchplayer'])->name('matchplayer.list');
        Route::get('matchcommentry/{id}',[\App\Http\Controllers\admin\MatchController::class,'matchcommentry'])->name('matchcommentry.list');
        Route::post('allmatchcommentrylist/{id}',[\App\Http\Controllers\admin\MatchController::class,'allmatchcommentrylist'])->name('allmatchcommentrylist');

        Route::get('matchscoreboard/{id}',[\App\Http\Controllers\admin\MatchController::class,'matchscoreboard'])->name('matchscoreboard.list');
        Route::post('allmatchscoreboardlist/{id}',[\App\Http\Controllers\admin\MatchController::class,'allmatchscoreboardlist'])->name('allmatchscoreboardlist');

        Route::get('country',[\App\Http\Controllers\admin\CountryController::class,'index'])->name('country.list');
        Route::post('addorupdatecountry',[\App\Http\Controllers\admin\CountryController::class,'addorupdatecountry'])->name('country.addorupdate');
        Route::post('allcountryslist',[\App\Http\Controllers\admin\CountryController::class,'allcountryslist'])->name('allcountryslist');
        Route::get('changecountrystatus/{id}',[\App\Http\Controllers\admin\CountryController::class,'changecountrystatus'])->name('country.changecountrystatus');
        Route::get('country/{id}/edit',[\App\Http\Controllers\admin\CountryController::class,'editcountry'])->name('country.edit');
        Route::get('country/{id}/delete',[\App\Http\Controllers\admin\CountryController::class,'deletecountry'])->name('country.delete');

        Route::get('tournament',[\App\Http\Controllers\admin\TournamentController::class,'index'])->name('tournament.list');
        Route::post('addorupdatetournament',[\App\Http\Controllers\admin\TournamentController::class,'addorupdatetournament'])->name('tournament.addorupdate');
        Route::post('alltournamentslist',[\App\Http\Controllers\admin\TournamentController::class,'alltournamentslist'])->name('alltournamentslist');
        Route::get('changetournamentstatus/{id}',[\App\Http\Controllers\admin\TournamentController::class,'changetournamentstatus'])->name('tournament.changetournamentstatus');
        Route::get('tournament/{id}/edit',[\App\Http\Controllers\admin\TournamentController::class,'edittournament'])->name('tournament.edit');
        Route::get('tournament/{id}/delete',[\App\Http\Controllers\admin\TournamentController::class,'deletetournament'])->name('tournament.delete');
        Route::get('tournament/sr_no',[\App\Http\Controllers\admin\TournamentController::class,'getsrno'])->name('tournament.getsrno');

        Route::get('team/{id}',[\App\Http\Controllers\admin\TeamController::class,'index'])->name('team.list');
        Route::post('addorupdateteam',[\App\Http\Controllers\admin\TeamController::class,'addorupdateteam'])->name('team.addorupdate');
        Route::post('allteamslist',[\App\Http\Controllers\admin\TeamController::class,'allteamslist'])->name('allteamslist');
        Route::get('changeteamstatus/{id}',[\App\Http\Controllers\admin\TeamController::class,'changeteamstatus'])->name('team.changeteamstatus');
        Route::get('team/{id}/edit',[\App\Http\Controllers\admin\TeamController::class,'editteam'])->name('team.edit');
        Route::get('team/{id}/delete',[\App\Http\Controllers\admin\TeamController::class,'deleteteam'])->name('team.delete');
        Route::get('team/sr_no/{id}',[\App\Http\Controllers\admin\TeamController::class,'getsrno'])->name('team.getsrno');

        Route::get('stadium',[\App\Http\Controllers\admin\StadiumController::class,'index'])->name('stadium.list');
        Route::post('addorupdatestadium',[\App\Http\Controllers\admin\StadiumController::class,'addorupdatestadium'])->name('stadium.addorupdate');
        Route::post('allstadiumslist',[\App\Http\Controllers\admin\StadiumController::class,'allstadiumslist'])->name('allstadiumslist');
        Route::get('changestadiumstatus/{id}',[\App\Http\Controllers\admin\StadiumController::class,'changestadiumstatus'])->name('stadium.changestadiumstatus');
        Route::get('stadium/{id}/edit',[\App\Http\Controllers\admin\StadiumController::class,'editstadium'])->name('stadium.edit');
        Route::get('stadium/{id}/delete',[\App\Http\Controllers\admin\StadiumController::class,'deletestadium'])->name('stadium.delete');

        Route::get('player/{id}',[\App\Http\Controllers\admin\PlayerController::class,'index'])->name('player.list');
        Route::post('addorupdateplayer',[\App\Http\Controllers\admin\PlayerController::class,'addorupdateplayer'])->name('player.addorupdate');
        Route::post('allplayerslist',[\App\Http\Controllers\admin\PlayerController::class,'allplayerslist'])->name('allplayerslist');
        Route::get('changeplayerstatus/{id}',[\App\Http\Controllers\admin\PlayerController::class,'changeplayerstatus'])->name('player.changeplayerstatus');
        Route::get('player/{id}/edit',[\App\Http\Controllers\admin\PlayerController::class,'editplayer'])->name('player.edit');
        Route::get('player/{id}/delete',[\App\Http\Controllers\admin\PlayerController::class,'deleteplayer'])->name('player.delete');

        Route::get('series',[\App\Http\Controllers\admin\SeriesController::class,'index'])->name('series.list');
        Route::post('addorupdateseries',[\App\Http\Controllers\admin\SeriesController::class,'addorupdateseries'])->name('series.addorupdate');
        Route::post('allseriesslist',[\App\Http\Controllers\admin\SeriesController::class,'allseriesslist'])->name('allseriesslist');
        Route::get('changeseriesstatus/{id}',[\App\Http\Controllers\admin\SeriesController::class,'changeseriesstatus'])->name('series.changeseriesstatus');
        Route::get('series/{id}/edit',[\App\Http\Controllers\admin\SeriesController::class,'editseries'])->name('series.edit');
        Route::get('series/{id}/delete',[\App\Http\Controllers\admin\SeriesController::class,'deleteseries'])->name('series.delete');

        Route::get('seriesteam/{id}',[\App\Http\Controllers\admin\SeriesTeamController::class,'index'])->name('seriesteam.list');
        Route::post('addorupdateseriesteam',[\App\Http\Controllers\admin\SeriesTeamController::class,'addorupdateseriesteam'])->name('seriesteam.addorupdate');
        Route::post('allseriesteamslist',[\App\Http\Controllers\admin\SeriesTeamController::class,'allseriesteamslist'])->name('allseriesteamslist');
        Route::get('changeseriesteamstatus/{id}',[\App\Http\Controllers\admin\SeriesTeamController::class,'changeseriesteamstatus'])->name('seriesteam.changeseriesteamstatus');
        Route::get('seriesteam/{id}/edit',[\App\Http\Controllers\admin\SeriesTeamController::class,'editseriesteam'])->name('seriesteam.edit');
        Route::get('seriesteam/{id}/delete',[\App\Http\Controllers\admin\SeriesTeamController::class,'deleteseriesteam'])->name('seriesteam.delete');

});

Route::group(['middleware'=>['auth']],function (){
    Route::get('profile',[\App\Http\Controllers\admin\ProfileController::class,'profile'])->name('profile');
    Route::get('profile/{id}/edit',[\App\Http\Controllers\admin\ProfileController::class,'edit'])->name('profile.edit');
    Route::post('profile/update',[\App\Http\Controllers\admin\ProfileController::class,'update'])->name('profile.update');

    
});