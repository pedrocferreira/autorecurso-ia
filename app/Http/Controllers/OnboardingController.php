<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    /**
     * Marca o onboarding como concluído para o usuário autenticado.
     */
    public function complete(Request $request)
    {
        $user = Auth::user();
        if ($user && !$user->onboarded) {
            $user->onboarded = true;
            $user->save();
        }

        return response()->json(['status' => 'ok']);
    }
} 