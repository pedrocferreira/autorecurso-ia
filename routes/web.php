<?php

use Illuminate\Support\Facades\Route;

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
// Route::post('/abacatepay/webhook', [AbacatePayController::class, 'webhook'])->name('abacatepay.webhook');

// Rota pública para download de recurso por CPF
// Route::get('/download/recurso/{appeal_id}/{cpf}', [PublicDownloadController::class, 'downloadRecurso'])
//     ->name('public.download.recurso');

// Rota para buscar justificativas de infrações via IA
Route::post('/infractions/justifications', [App\Http\Controllers\InfractionJustificationController::class, 'generateJustifications'])
    ->middleware('auth')
    ->name('infractions.justifications');

// Rota para justificativas contextualizadas baseadas nos dados da multa
Route::post('/infractions/justifications/contextualized', [App\Http\Controllers\InfractionJustificationController::class, 'generateContextualizedJustifications'])
    ->middleware('auth')
    ->name('infractions.justifications.contextualized');

// Rota para detecção automática do tipo de infração
Route::post('/infractions/detect-type', [App\Http\Controllers\InfractionDetectionController::class, 'detectInfractionType'])
    ->middleware('auth')
    ->name('infractions.detect-type');

// Rota para extração de dados de documentos (uploads públicos)
Route::post('/extract-document-data', [App\Http\Controllers\DocumentExtractionController::class, 'extract']);
Route::get('/test-simple', function () {
    return view('appeals.test_simple');
})->name('test.simple');

Route::get('/test-upload-simple', function () {
    return view('appeals.create_new_simple');
})->name('test.upload_simple');

Route::get('/vehicle-upload', function () {
    return view('appeals.vehicle_upload');
})->name('vehicle.upload');

// Rotas de autenticação Google
Route::get('auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])
    ->name('auth.google');
Route::get('auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');

// Incluir rotas de autenticação
require __DIR__.'/auth.php';

// Incluir rotas da área autenticada
require __DIR__.'/authenticated.php'; 