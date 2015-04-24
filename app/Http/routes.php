<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);


/**
 * Routes for otentikasi
 */
Route::get('/otentik/login', ['uses' => 'Otentik\OtentikController@loginShow']);
Route::post('/otentik/login', ['uses' => 'Otentik\OtentikController@loginAction']);

Route::get('/otentik/register', ['uses' => 'Otentik\OtentikController@registerShow']);
Route::post('/otentik/register', ['uses' => 'Otentik\OtentikController@registerAction']);

Route::get('/otentik/forgot_password', ['uses' => 'Otentik\OtentikController@forgotPasswordShow']);
Route::post('/otentik/forgot_password', ['uses' => 'Otentik\OtentikController@forgotPasswordAction']);

Route::get('/otentik/reset_password/{user_id}/{key}', ['uses' => 'Otentik\OtentikController@resetPasswordShow'])->where('user_id', '[0-9]+');
Route::post('/otentik/reset_password', ['uses' => 'Otentik\OtentikController@resetPasswordAction']);

Route::get('/otentik/activate/{user_id}/{key}', ['uses' => 'Otentik\OtentikController@activateAction'])->where('user_id', '[0-9]+');

Route::get('/otentik/logout', ['uses' => 'OtentikasiController@logoutAction']);

Route::get('/otentik/send_again', ['uses' => 'OtentikasiController@sendAgainShow']);
Route::post('/otentik/send_again', ['uses' => 'OtentikasiController@sendAgainAction']);

Route::get('/otentik', function(){
	return redirect('otentik/dashboard');
});
Route::get('/otentik/dashboard', ['uses' => 'OtentikasiController@dashboardShow']);
Route::get('/otentik/myprofile', ['uses' => 'OtentikasiController@myProfileShow']);
Route::post('/otentik/profile', ['uses' => 'OtentikasiController@updateProfileAction']);
Route::post('/otentik/account', ['uses' => 'OtentikasiController@updateAccountAction']);