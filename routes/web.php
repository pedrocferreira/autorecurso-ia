<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbacatePayController;

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

// Incluir rotas de autenticação
require __DIR__.'/auth.php';

// Incluir rotas da área autenticada
require __DIR__.'/authenticated.php'; 