<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
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



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/editsensors', function () {
    return view('editsensors');
})->middleware(['auth', 'verified'])->name('editsensors');

Route::get('/activitylog', function () {
    return view('activitylog');
})->middleware(['auth', 'verified'])->name('activitylog');


Route::post('/editsensors', [SensorController::class, 'store'])->name('editsensors.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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


require __DIR__.'/auth.php';
