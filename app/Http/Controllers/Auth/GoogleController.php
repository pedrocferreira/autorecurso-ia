<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            Log::info('🔐 Tentativa de login Google', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId()
            ]);

            // Buscar usuário existente por Google ID ou email
            $user = User::where('google_id', $googleUser->getId())
                       ->orWhere('email', $googleUser->getEmail())
                       ->first();

            if (!$user) {
                // Criar novo usuário
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(), // Google já verifica o email
                    'credits' => 1000, // Créditos iniciais
                    'password' => Hash::make(uniqid()), // Senha aleatória
                ]);

                Log::info('✅ Novo usuário criado via Google', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            } else {
                // Atualizar dados do usuário existente
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'name' => $googleUser->getName(),
                ]);

                Log::info('✅ Usuário existente logado via Google', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }

            // Fazer login
            Auth::login($user);

            Log::info('🎉 Login Google realizado com sucesso', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            Log::error('❌ Erro no login Google', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect('/login')->with('error', 'Erro ao fazer login com Google. Tente novamente.');
        }
    }
} 