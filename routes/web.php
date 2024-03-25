<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Models\Post; 
use App\Http\Controllers\SensorController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/info', function () {
    phpinfo();
});

Route::get('/post/{slug}', [PostController::class, 'show']);

Route::get('/sensor-data-json', function () {
    $sensorData = Post::all(); // Fetch all sensor data

    return response()->json($sensorData);
});

Route::get('/sensor-data', function () {
    $sensorData = Post::all(); // Fetch all sensor data

    return view('sensor-data')->with('sensorData', $sensorData);
});


Route::post('/save-alert-data', 'App\Http\Controllers\SensorController@saveAlertData');

