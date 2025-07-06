<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbacatePayController;
use App\Http\Controllers\PublicDownloadController;

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

// Webhook da AbacatePay (sem autenticação)
Route::post('/abacatepay/webhook', [AbacatePayController::class, 'webhook'])->name('abacatepay.webhook');

// Rota pública para download de recurso por CPF
Route::get('/download/recurso/{appeal_id}/{cpf}', [PublicDownloadController::class, 'downloadRecurso'])
    ->name('public.download.recurso');

// Incluir rotas de autenticação
require __DIR__.'/auth.php';

// Incluir rotas da área autenticada
require __DIR__.'/authenticated.php'; 