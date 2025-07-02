<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Validação dos dados de entrada
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'terms' => ['required', 'accepted'],
            ], [
                'email.unique' => 'Este endereço de e-mail já está sendo utilizado.',
                'terms.accepted' => 'Você deve aceitar os termos de serviço.',
                'password.confirmed' => 'A confirmação da senha não confere.',
            ]);
    
            // Criar o usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'credits' => 0, // Sem créditos grátis para novos usuários
            ]);
    
            // Disparar evento de registro
            event(new Registered($user));
    
            // Logar o usuário automaticamente
            Auth::login($user);
    
            // Log de sucesso
            Log::info('Novo usuário registrado', ['user_id' => $user->id, 'email' => $user->email]);

            return redirect(RouteServiceProvider::HOME)->with('success', 'Conta criada com sucesso!');
            
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            // Log do erro
            Log::error('Erro ao registrar usuário: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'Ocorreu um erro ao processar seu registro. Por favor, tente novamente.']);
        }
    }
}
