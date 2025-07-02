<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AppealController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
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

// Rota de teste
Route::get('/test', function() {
    return 'Rota de teste funcionando!';
});

// Rotas autenticadas
Route::middleware(['auth', 'verified', 'profile.completed'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Multas (Tickets)
    Route::resource('tickets', TicketController::class);

    // Recursos (Appeals)
    Route::get('/appeals/new/create', [AppealController::class, 'createNew'])->name('appeals.create_new');
    Route::post('/appeals/new/store', [AppealController::class, 'storeNew'])->name('appeals.store_new');
    Route::get('tickets/{ticket}/appeal', [AppealController::class, 'create'])->name('tickets.create_appeal');
    Route::resource('appeals', AppealController::class)->except(['create', 'store']);
    Route::get('/appeals/{appeal}/download', [AppealController::class, 'download'])->name('appeals.download');

    // Créditos
    Route::get('/credits', [\App\Http\Controllers\CreditController::class, 'index'])->name('credits.index');
    Route::get('/credits/packages', [\App\Http\Controllers\CreditController::class, 'packages'])->name('credits.packages');
    Route::post('/credits/purchase', [\App\Http\Controllers\CreditController::class, 'purchase'])->name('credits.purchase');

    Route::post('/credits/checkout', [\App\Http\Controllers\StripeController::class, 'checkout'])->middleware('auth')->name('credits.checkout');
    Route::get('/credits/success', [\App\Http\Controllers\StripeController::class, 'success'])->name('credits.success');
    Route::get('/credits/cancel', [\App\Http\Controllers\StripeController::class, 'cancel'])->name('credits.cancel');

    // Rotas AbacatePay (PIX)
    Route::get('/credits/payment/form', [\App\Http\Controllers\AbacatePayController::class, 'showPaymentForm'])->name('credits.payment.form');
    Route::post('/credits/pix/payment', [\App\Http\Controllers\AbacatePayController::class, 'createPixPayment'])->name('credits.pix.payment');
    Route::post('/credits/pix/status', [\App\Http\Controllers\AbacatePayController::class, 'checkPaymentStatus'])->name('credits.pix.status');
    Route::get('/credits/pix/success', [\App\Http\Controllers\AbacatePayController::class, 'success'])->name('credits.pix.success');
    Route::get('/credits/pix/cancel', [\App\Http\Controllers\AbacatePayController::class, 'cancel'])->name('credits.pix.cancel');
});

// Rotas de administração
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/tickets', [AdminController::class, 'tickets'])->name('tickets');
    Route::get('/appeals', [AdminController::class, 'appeals'])->name('appeals');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/credits', [UserController::class, 'credit'])->name('users.credit');
    Route::post('/users/{user}/toggle-block', [UserController::class, 'toggleBlock'])->name('users.toggle_block');
});

// Rotas de perfil
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/stripe/webhook', [\App\Http\Controllers\StripeController::class, 'webhook'])->name('stripe.webhook');

// Webhook AbacatePay (fora do middleware de autenticação)
Route::post('/abacatepay/webhook', [\App\Http\Controllers\AbacatePayController::class, 'webhook'])->name('abacatepay.webhook');

// Rotas para documentos legais
Route::get('/legal/privacy-policy', function () {
    return view('legal.privacy-policy');
})->name('legal.privacy');

Route::get('/legal/terms-of-service', function () {
    return view('legal.terms-of-service');
})->name('legal.terms');

Route::get('/legal/cookie-policy', function () {
    return view('legal.cookie-policy');
})->name('legal.cookies');

// Rota para solicitação de dados pessoais (LGPD)
Route::get('/privacy/request', function () {
    return view('privacy.request');
})->name('privacy.request');

Route::middleware(['auth'])->group(function () {
    Route::post('/onboarding/complete', [\App\Http\Controllers\OnboardingController::class, 'complete'])->name('onboarding.complete');
});

require __DIR__.'/auth.php';
