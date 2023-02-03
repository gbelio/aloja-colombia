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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/alojar', function () {
    return view('alojar');
});

Auth::routes();

#Route::get('/home', 'HomeController@index')->name('home');

Route::get('/alojamientos/busqueda', 'AlojamientosController@busqueda');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/config-cache', function () {
        $exitCode = Artisan::call('config:cache');
        return '<h1>Cache Config cleared</h1>';
    });

    Route::get('/app-cache', function () {
        $exitCode = Artisan::call('cache:clear');
        return '<h1>Cache App cleared</h1>';
    });

    Route::get('/view-cache', function () {
        $exitCode = Artisan::call('view:clear');
        return '<h1>View App cleared</h1>';
    });

    Route::resource('/alojamientos', 'AlojamientosController');
    Route::resource('/alojamientosPedidos', 'AlojamientosPedidosController');
    Route::get('/alojamientos/{id}/activar', 'AlojamientosController@activar');
    Route::get(
        '/alojamientos/{id}/inactivar',
        'AlojamientosController@inactivar'
    );
    Route::post(
        '/alojamientos/{id}/reservar',
        'AlojamientosController@reservar'
    );
    Route::get('/images/{id}', 'AlojamientosController@images');
    Route::post('/images/update', 'AlojamientosController@imagesUpdate');
    Route::post('/images/save', 'AlojamientosController@saveImages');

    Route::get('/home', function () {
        return redirect('alojamientos');
    });

    Route::resource('/users', 'UsersController');
    Route::get('/changePassword', 'UsersController@showChangePasswordForm');
    Route::post('/changePassword', 'UsersController@changePassword')->name(
        'changePassword'
    );
});

Route::resource('/alojamientos', 'AlojamientosController', [
    'only' => ['show'],
]);

//MERCADO PAGO
Route::post('/card_process_payment', [
    App\Http\Controllers\PaymentController::class,
    'cardProcessPayment',
]);
Route::post('/cash_process_payment', [
    App\Http\Controllers\PaymentController::class,
    'cashProcessPayment',
]);

Route::post('/webhooks', 'WebhookController@handle');

//PHP ARTISAN COMMANDS
Route::get('/optimize', function() {
    $output = [];
    \Artisan::call('optimize', $output);
});

Route::get('/statistics', 'StatisticsController@showAll')->middleware('admin');
Route::get('/statistics/{id}', 'StatisticsController@show')->middleware('auth');