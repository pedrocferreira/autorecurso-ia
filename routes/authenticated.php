<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AppealController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OnboardingController;

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
|
| Rotas que requerem autenticação
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Tickets (Multas)
    Route::resource('tickets', TicketController::class);
    Route::get('/tickets/{ticket}/create-appeal', [AppealController::class, 'createFromTicket'])->name('tickets.create_appeal');
    
    // Appeals (Recursos)
    Route::resource('appeals', AppealController::class);
    Route::get('/appeals/create-new', [AppealController::class, 'createNew'])->name('appeals.create_new');
    Route::post('/appeals/store-new', [AppealController::class, 'storeNew'])->name('appeals.store_new');
    Route::get('/appeals/{appeal}/download', [AppealController::class, 'download'])->name('appeals.download');
    
    // Credits (Créditos)
    Route::get('/credits', [CreditController::class, 'index'])->name('credits.index');
    Route::get('/credits/packages', [CreditController::class, 'packages'])->name('credits.packages');
    Route::get('/credits/payment-form', [CreditController::class, 'paymentForm'])->name('credits.payment.form');
    Route::post('/credits/checkout', [CreditController::class, 'checkout'])->name('credits.checkout');
    Route::post('/credits/pix/payment', [CreditController::class, 'pixPayment'])->name('credits.pix.payment');
    Route::get('/credits/pix/status', [CreditController::class, 'pixStatus'])->name('credits.pix.status');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Onboarding
    Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
    
});

// Rotas para páginas legais (não requerem autenticação)
Route::get('/privacy-policy', function () {
    return view('legal.privacy-policy');
})->name('legal.privacy');

Route::get('/terms-of-service', function () {
    return view('legal.terms-of-service');
})->name('legal.terms');

Route::get('/cookie-policy', function () {
    return view('legal.cookie-policy');
})->name('legal.cookies');

Route::get('/privacy-request', function () {
    return view('legal.privacy-request');
})->name('privacy.request');

// Rotas do self-service (cliente)
Route::prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/', function () {
        return redirect('/cliente/wizard');
    })->name('index');
    
    Route::get('/wizard', function () {
        $infractionOptions = \App\Models\InfractionType::where('active', true)->get();
        return view('self-service.wizard', compact('infractionOptions'));
    })->name('wizard');
    
    Route::get('/sucesso', function () {
        return view('self-service.success');
    })->name('success');
    
    // API Routes para o wizard
    Route::get('/api/infraction-types', function () {
        $infractions = \App\Models\InfractionType::where('active', true)
            ->select('id', 'code', 'description', 'base_amount', 'points', 'severity', 'law_article')
            ->orderBy('code')
            ->get();
        return response()->json(['success' => true, 'data' => $infractions]);
    })->name('api.infractions');
});

// Rotas de chat/pagamento (não precisam de auth)
Route::post('/chat/pix', [App\Http\Controllers\Api\ChatPaymentController::class, 'createPix']);
Route::get('/chat/pix/{id}/status', [App\Http\Controllers\Api\ChatPaymentController::class, 'checkStatus']);
Route::post('/chat/stripe', [App\Http\Controllers\Api\ChatStripeController::class, 'createSession']);

// Rota para consulta de placa
Route::post('/api/vehicle/lookup', [App\Http\Controllers\Api\VehicleController::class, 'lookup']);

// Rotas de administração (se necessário)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', function () {
        return view('admin.users');
    })->name('users');
    
    Route::get('/users/{user}/edit', function () {
        return view('admin.user_edit');
    })->name('users.edit');
    
    Route::patch('/users/{user}', function () {
        return redirect()->back();
    })->name('users.update');
    
    Route::post('/users/{user}/toggle-block', function () {
        return redirect()->back();
    })->name('users.toggle_block');
    
    Route::post('/users/{user}/credit', function () {
        return redirect()->back();
    })->name('users.credit');
}); 