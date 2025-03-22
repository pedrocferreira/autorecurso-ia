<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AppealController;
use App\Http\Controllers\Admin\AdminController;
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

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Créditos
    Route::get('/credits', [\App\Http\Controllers\CreditController::class, 'index'])->name('credits.index');
    Route::get('/credits/packages', [\App\Http\Controllers\CreditController::class, 'packages'])->name('credits.packages');
    Route::post('/credits/purchase', [\App\Http\Controllers\CreditController::class, 'purchase'])->name('credits.purchase');

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

Route::middleware(['auth'])->group(function () {
    // Rotas de multas
    Route::resource('tickets', TicketController::class);
    
    // Rotas de recursos
    Route::get('tickets/{ticket}/appeal', [AppealController::class, 'create'])->name('appeals.create');
    Route::post('appeals', [AppealController::class, 'store'])->name('appeals.store');
    Route::get('appeals/{appeal}', [AppealController::class, 'show'])->name('appeals.show');
});

require __DIR__.'/auth.php';
