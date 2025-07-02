<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

class SocialAuthController extends Controller
{
    /**
     * Redireciona o usuário para o Google
     */
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch (Exception $e) {
            Log::error('Erro ao redirecionar para Google: ' . $e->getMessage());
            return redirect()->route('register')
                ->with('error', 'Erro ao conectar com Google. Tente novamente.');
        }
    }

    /**
     * Lida com o callback do Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Verificar se o usuário já existe com este email
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // Se existe, apenas faz login
                Auth::login($user);
                
                Log::info('Usuário existente logado via Google', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                
                // Verificar se precisa completar o perfil
                return $this->redirectAfterLogin($user, 'Login realizado com sucesso!');
                
            } else {
                // Se não existe, cria um novo usuário
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)), // Senha aleatória para conta OAuth
                    'email_verified_at' => now(), // Marcamos como verificado já que o Google já validou
                    'credits' => 0, // Sem créditos grátis
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
                
                Auth::login($user);
                
                Log::info('Novo usuário criado via Google', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                
                // Para usuários novos do Google, sempre precisa completar o perfil
                return redirect()->route('profile.edit')
                    ->with('success', 'Conta criada com sucesso!')
                    ->with('info', 'Para continuar, precisamos de algumas informações adicionais para completar seu perfil.');
            }
            
        } catch (Exception $e) {
            Log::error('Erro no callback do Google: ' . $e->getMessage());
            return redirect()->route('register')
                ->with('error', 'Erro ao processar login com Google. Tente novamente.');
        }
    }

    /**
     * Verifica se o perfil está completo e redireciona adequadamente
     */
    private function redirectAfterLogin($user, $message)
    {
        $profileIsComplete = !empty($user->cpf) && 
                            !empty($user->cnh_category) && 
                            !empty($user->cnh_address) && 
                            !empty($user->phone);

        if ($profileIsComplete) {
            return redirect()->route('dashboard')->with('success', $message);
        } else {
            return redirect()->route('profile.edit')
                ->with('success', $message)
                ->with('info', 'Para acessar todas as funcionalidades, complete seu perfil com as informações adicionais.');
        }
    }
} 