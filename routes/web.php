<?php

use App\Http\Controllers\WorkspaceController;
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

Route::get('/', [WorkspaceController::class, 'index']);
Route::get('/addFilter/{key}/{value}', [WorkspaceController::class, 'addFilter'])->name('addFilter');
Route::get('/removeFilter/{key}/{value}', [WorkspaceController::class, 'removeFilter'])->name('removeFilter');
