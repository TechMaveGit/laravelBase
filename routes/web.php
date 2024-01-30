<?php

use Illuminate\Support\Facades\Route;


use Illuminate\Support\Facades\App;
use App\Http\Controllers\TestController;
/*
|--------------------------------------------------------------------------
| Web Routes Start
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/',[WebController::class, 'home']);




/*
|--------------------------------------------------------------------------
| Web Routes End
|--------------------------------------------------------------------------
*/








/*
|--------------------------------------------------------------------------
| Testing Routes
|--------------------------------------------------------------------------
*/
if (App::environment('local')) {
    Route::get('/test/storage-links',[TestController::class, 'storageLink']);
    Route::get('/test/clear-data', [TestController::class, 'clearData']);
    Route::get('/test/mail-check', [TestController::class, 'mailCheck']);
}
