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

Route::get('/users', [UserController::class, 'index']);
// Rotas para o recurso de Clientes
Route::apiResource('/clients', ClientController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);

// Rotas para o recurso de Serviços
Route::apiResource('/services', ServiceController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);

// Rotas para o recurso de Agendamentos
Route::apiResource('/appointments', AppointmentController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);
// Rotas para o recurso de Agendamentos e Serviços (tabela pivot)
// Usando 'shallow' para evitar rotas aninhadas desnecessárias
// Isso permite acessar os serviços de um agendamento sem precisar do ID do agendamento na URL
// Exemplo: GET /appointments/{appointment}/services
Route::apiResource('/work-schedule', WorkScheduleController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);

Route::apiResource('/work-break', WorkBreakController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);

Route::apiResource('/users', UserController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);

Route::apiResource('/payments', PaymentController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);

// routes/api.php
Route::get('/availability', [AppointmentController::class, 'checkAvailability']);