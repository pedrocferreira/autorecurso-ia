<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $profileIsIncomplete = empty($user->cpf) || 
                                   empty($user->cnh_category) || 
                                   empty($user->cnh_address) || 
                                   empty($user->phone);

            // Verifica também se a requisição é para o método update do perfil para evitar loop de redirecionamento
            if ($profileIsIncomplete && !$request->routeIs('profile.edit') && !$request->is('profile') /* Para o PATCH request */) {
                // Adiciona uma mensagem flash para ser exibida na página de perfil
                session()->flash('warning', 'Por favor, complete seu perfil para continuar.');
                return redirect()->route('profile.edit');
            }
        }

        return $next($request);
    }
}
