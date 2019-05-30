<?php

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

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');

Route::get('/404', function () {
    return view('Error.404');
})->name('error.404');

Auth::routes();

Route::get('/users-list', 'Admin\AdminController@getUsers')->name('users.list');
Route::get('/users', 'Admin\AdminController@showUsersPage')->name('users.page');
Route::get('/users-edit', 'Admin\AdminController@editUser')->name('users.edit');
Route::get('/users-delete', 'Admin\AdminController@deleteUser')->name('users.delete');
Route::get('/users-add', 'Admin\AdminController@addUser')->name('users.add');
Route::get('/users-change-pass', 'Admin\AdminController@requestPassword')->name('users.change.pass');

Route::get('/password-request/{TokenHash}', 'Auth\ResetPasswordController@validatePasswordRequest')->name('password.request');
Route::post('/password-request-activate', 'Auth\ResetPasswordController@activateAccount')->name('password.request.activate');

Route::get('/records', 'Listing\ListingsController@manageRecords')->name('listings.manage.page');
Route::post('/records-list', 'Listing\ListingsController@getRecords')->name('listings.list');
Route::get('/records-edit', 'Listing\ListingsController@editRecords')->name('listings.edit');
Route::get('/records-delete', 'Listing\ListingsController@deleteRecords')->name('listings.delete');
Route::get('/records-new', 'Listing\ListingsController@newRecords')->name('listings.new');

/* Map Search */
Route::get('/mapsearch', 'Listing\MapSearchController@index')->name('listings.mapsearch.page');
Route::get('/mapsearch/filters', 'Listing\MapSearchController@getFilters')->name('map.search.filters');
Route::get('/mapsearch/autocomplete', 'Listing\MapSearchController@autoComplete')->name('map.autocomplete');
Route::get('/mapsearch/search', 'Listing\MapSearchController@searchAND')->name('map.search');
Route::get('/mapsearch/locs', 'Listing\MapSearchController@getLocs');
Route::get('/mapsearch/marker/details/{id}', 'Listing\MapSearchController@getMarkerDetails')->name('map.search.marker.details');
Route::post('/mapsearch/complete-search', 'Listing\MapSearchController@completeSearchAND')->name('map.search.completeSearch');
Route::post('/mapsearch/upload', 'Listing\MapSearchController@uploadImage')->name('map.search.upload');
Route::get('/mapsearch/index','Listing\MapSearchController@getBlankMap')->name('map.search.index');
Route::post('/mapsearch/filterswithsearch','Listing\MapSearchController@getFiltersWithSearch')->name('map.search.filterswithsearch');
Route::post('/mapsearch/clearfilters','Listing\MapSearchController@getFiltersClean')->name('map.search.clearfilters');
Route::post('/mapsearch/rangemax', 'Listing\MapSearchController@getInRangeMax')->name('map.rangemax');

/* Support */
Route::post('/support', 'Support\SupportController@supportMailer')->name('support');
Route::get('/support/captcha', 'Support\SupportController@getCaptcha')->name('support-captcha');

/* Import/Export */
Route::get('/records/samplecsv', 'Admin\ImportExportController@getSampleCSVWithHeaders')->name('listings.samplecsv');
Route::get('/records/exportcsv', 'Admin\ImportExportController@export')->name('listings.exportcsv');
Route::post('/records/uploadcsv', 'Admin\ImportExportController@upload')->name('listings.uploadcsv');
Route::post('/records/importcsv', 'Admin\ImportExportController@import')->name('listings.importcsv');
Route::post('/records/importcleanup', 'Admin\ImportExportController@cleanup')->name('listings.importcleanup');

/*Route::get('/login', 'Auth\LoginController@showLoginForm');
Route::post('/login', 'Auth\LoginController@login')->name('login');
Route::get('/resetPassword', 'Auth\ResetPasswordController@showResetForm');
Route::post('/resetPassword', 'Auth\ResetPasswordController@reset')->name('password.request');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');*/
