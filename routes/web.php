<?php

use Illuminate\Support\Facades\Route;
Route::get('/', 'Auth\Admin\LoginController@login')->name('admin.auth.login');

// Admin Auth
Route::get('login', 'Auth\Admin\LoginController@login')->name('admin.auth.login');
Route::post('login', 'Auth\Admin\LoginController@loginAdmin')->name('admin.auth.loginAdmin');
Route::post('logout', 'Auth\Admin\LoginController@logout')->name('admin.auth.logout');
Route::get('logout', 'Auth\Admin\LoginController@logout');

// Admin Dashborad
Route::group([
'namespace' => 'Backend\Admin',
'prefix' => 'admin',
'as' => 'admin.',
'middleware' => 'auth:admin'],
function () {
    require base_path('routes/backend/admin.php');
});