<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ClientTasksController;

Route::apiResource('clients', ClientController::class)->names([
    'index'  => 'api.clients.index',
    'store'  => 'api.clients.store',
    'show'   => 'api.clients.show',
    'update' => 'api.clients.update',
    'destroy'=> 'api.clients.destroy',
]);

Route::prefix('clients/{client}/tasks')->group(function () {
    Route::get('/', [ClientTasksController::class, 'index']);
    Route::post('/', [ClientTasksController::class, 'store']);
    Route::get('/{task}', [ClientTasksController::class, 'show']);
    Route::put('/{task}', [ClientTasksController::class, 'update']);
    Route::delete('/{task}', [ClientTasksController::class, 'destroy']);
});
