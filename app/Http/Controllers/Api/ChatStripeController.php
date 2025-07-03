<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatStripeController extends Controller
{
    public function createSession(Request $request)
    {
        // Por enquanto, vamos retornar um erro simulado
        // porque os services de pagamento foram removidos
        return response()->json([
            'success' => false,
            'message' => 'Serviço de pagamento temporariamente indisponível. Tente novamente mais tarde.'
        ], 503);
    }
} 