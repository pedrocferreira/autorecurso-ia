<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AppealController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MultaImageController;

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
|
| Rotas que requerem autenticação
|
*/

Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Tickets (Multas)
    Route::resource('tickets', TicketController::class);
    Route::get('/tickets/{ticket}/create-appeal', [AppealController::class, 'createFromTicket'])->name('tickets.create_appeal');
    
    // Busca Automática de Multas
    Route::get('/traffic-tickets', [App\Http\Controllers\TrafficTicketController::class, 'index'])->name('traffic-tickets.index');
    Route::post('/traffic-tickets/search-cpf', [App\Http\Controllers\TrafficTicketController::class, 'searchByCpf'])->name('traffic-tickets.search-cpf');
    Route::post('/traffic-tickets/search-cnh', [App\Http\Controllers\TrafficTicketController::class, 'searchByCnh'])->name('traffic-tickets.search-cnh');
    Route::post('/traffic-tickets/search-plate', [App\Http\Controllers\TrafficTicketController::class, 'searchByPlate'])->name('traffic-tickets.search-plate');
    Route::post('/traffic-tickets/search-all', [App\Http\Controllers\TrafficTicketController::class, 'searchAll'])->name('traffic-tickets.search-all');
    Route::get('/traffic-tickets/statistics', [App\Http\Controllers\TrafficTicketController::class, 'statistics'])->name('traffic-tickets.statistics');
    
    // Appeals (Recursos) - Rotas específicas primeiro para evitar conflitos
    Route::get('/appeals/create-new', [AppealController::class, 'createNew'])->name('appeals.create_new');
Route::post('/appeals/create-new', [AppealController::class, 'storeNew'])->name('appeals.store_new');
    Route::get('/appeals/{appeal}/download/{format}', [AppealController::class, 'download'])->name('appeals.download');
    // Route redundante removida para permitir cache de rotas
    // Route::get('/appeals/{appeal}/download', [AppealController::class, 'download'])->name('appeals.download');
    
    // Processamento de imagem de multa com Gemini Vision
    Route::post('/multa/process-image', [MultaImageController::class, 'processImage'])->name('multa.process_image');
    
    // Rota de fallback para appeals/create que redireciona para create-new
    Route::get('/appeals/create', function () {
        return redirect()->route('appeals.create_new')->with('info', 'Redirecionado para o novo formulário de recursos. Use esta página para gerar recursos com IA.');
    })->name('appeals.create');
    
    // Appeals (Recursos) - Resource routes exceto create que será substituído por create-new
    Route::get('/appeals', [AppealController::class, 'index'])->name('appeals.index');
    Route::post('/appeals', [AppealController::class, 'store'])->name('appeals.store');
    Route::get('/appeals/{appeal}', [AppealController::class, 'show'])->name('appeals.show');
    Route::get('/appeals/{appeal}/edit', [AppealController::class, 'edit'])->name('appeals.edit');
    Route::patch('/appeals/{appeal}', [AppealController::class, 'update'])->name('appeals.update');
    Route::delete('/appeals/{appeal}', [AppealController::class, 'destroy'])->name('appeals.destroy');
    
    // Assinatura (Planos)
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Onboarding desativado (controller ausente)
    // Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
    
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
        return view('self-service.landing-simple');
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

// Rotas de chat/pagamento desativadas temporariamente
// Route::post('/chat/pix', [App\Http\Controllers\ChatController::class, 'createPixPayment']);
// Route::get('/chat/pix/{id}/status', [App\Http\Controllers\ChatController::class, 'checkPixStatus']);
// Route::post('/chat/stripe', [App\Http\Controllers\ChatController::class, 'createStripePayment']);

// Rotas de sessão de chat desativadas temporariamente
// Route::get('/chat/session/{uuid}', [App\Http\Controllers\ChatSessionController::class, 'show']);
// Route::post('/chat/message', [App\Http\Controllers\ChatSessionController::class, 'store']);

// Webhook do chat (sem auth)
// Route::post('/chat/webhook/abacatepay', [App\Http\Controllers\ChatController::class, 'webhookAbacatePay'])
//     ->name('chat.webhook.abacatepay')
//     ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Rota para consulta de placa
Route::post('/api/vehicle/lookup', [App\Http\Controllers\Api\VehicleController::class, 'lookup']);

// Rotas de administração
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users');
    Route::get('/tickets', [App\Http\Controllers\Admin\AdminController::class, 'tickets'])->name('tickets');
    Route::get('/appeals', [App\Http\Controllers\Admin\AdminController::class, 'appeals'])->name('appeals');
    
    // Gerenciamento de usuários
    Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/toggle-block', [App\Http\Controllers\Admin\UserController::class, 'toggleBlock'])->name('users.toggle_block');
    // Removido fluxo de créditos
});