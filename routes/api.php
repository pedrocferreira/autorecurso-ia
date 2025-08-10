<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (placeholder)
|--------------------------------------------------------------------------
| Este arquivo existe apenas para satisfazer o carregamento automático do
| Laravel após a remoção das rotas originais. Substitua-o pelas rotas reais
| da sua aplicação quando necessário.
*/

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Rotas para dados do veículo
Route::post('/vehicle-data', [App\Http\Controllers\Api\VehicleController::class, 'lookup']); 