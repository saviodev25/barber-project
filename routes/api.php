<?php
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkBreakController;
use App\Http\Controllers\Api\WorkScheduleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('/users', UserController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);

// Rotas para o recurso de Clientes
Route::apiResource('/clients', ClientController::class)//OK
    ->only(['index', 'show', 'store', 'update', 'destroy']);

// Rotas para o recurso de Serviços
Route::apiResource('/services', ServiceController::class)//Ok
    ->only(['index', 'show', 'store', 'update', 'destroy']);

// Rotas para o recurso de Agendamentos
Route::apiResource('/appointments', AppointmentController::class)//OK
    ->only(['index', 'show', 'store', 'update', 'destroy']);

Route::apiResource('/work-schedule', WorkScheduleController::class)//Falta Update funcionar
    ->only(['index', 'show', 'store', 'update', 'destroy']);

Route::apiResource('/work-break', WorkBreakController::class)//falta update
    ->only(['index', 'show', 'store', 'update', 'destroy']);

Route::apiResource('/payments', PaymentController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);

Route::get('/availability', [AppointmentController::class, 'checkAvailability']);