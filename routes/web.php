<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportDataController;
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

Route::resource('reports',ReportController::class);
Route::get('importExportView', [ReportDataController::class, 'importExportView']);
Route::get('myexport', [ReportDataController::class, 'export'])->name('export');

