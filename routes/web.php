<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AppealController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\WebhookController;

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
})->name('welcome');

// Rotas autenticadas
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Multas (Tickets)
    Route::resource('tickets', TicketController::class);

    // Recursos (Appeals)
    Route::get('/appeals/new/create', [AppealController::class, 'createNew'])->name('appeals.create_new');
    Route::resource('appeals', AppealController::class);
    Route::get('/appeals/{appeal}/download', [AppealController::class, 'download'])->name('appeals.download');
    Route::get('tickets/{ticket}/appeal', [AppealController::class, 'create'])->name('appeals.create');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas de créditos
    Route::get('/credits', [CreditController::class, 'index'])->name('credits.index');
    Route::get('/credits/packages', [CreditController::class, 'packages'])->name('credits.packages');
    Route::post('/credits/purchase', [CreditController::class, 'purchase'])->name('credits.purchase');
    Route::get('/credits/success', [CreditController::class, 'success'])->name('credits.success');
    Route::get('/credits/history', [CreditController::class, 'history'])->name('credits.history');

    // Rota temporária para adicionar créditos gratuitos para testes
    if (app()->environment(['local', 'development'])) {
        Route::get('/credits/free', [\App\Http\Controllers\CreditController::class, 'addFreeCredits'])->name('credits.free');
    }
});

// Rotas de administração
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/tickets', [AdminController::class, 'tickets'])->name('tickets');
    Route::get('/appeals', [AdminController::class, 'appeals'])->name('appeals');
});

Route::post('/webhook/stripe', [WebhookController::class, 'handleStripeWebhook']);

// Rotas de Documentos Legais
Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

require __DIR__.'/auth.php';
