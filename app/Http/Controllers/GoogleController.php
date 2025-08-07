<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

            // Verificar se o usuário já existe
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Criar novo usuário
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(16)),
                    'google_id' => $googleUser->id,
                    'email_verified_at' => now(), // Usuários do Google já são verificados
                    'credits' => 3, // Créditos iniciais
                ]);
            } else {
                // Atualizar google_id se não existir
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            }

            // Fazer login do usuário
            Auth::login($user);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            \Log::error('Erro na autenticação Google: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Erro ao fazer login com Google. Tente novamente.');
        }
    }
} 