<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransferController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/historico', [TransferController::class, 'index']);
Route::post('/transferir', [TransferController::class, 'store']);
Route::get('/detalhe/{id}', [TransferController::class, 'show']);
Route::put('/editar/{id}', [TransferController::class, 'update']);
Route::delete('/deletar/{id}', [TransferController::class, 'destroy']);