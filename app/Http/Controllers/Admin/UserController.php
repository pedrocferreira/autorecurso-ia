<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Formulário de edição do usuário.
     */
    public function edit(User $user): View
    {
        return view('admin.user_edit', compact('user'));
    }

    /**
     * Atualiza informações básicas do usuário.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'is_admin' => 'nullable|boolean',
            'blocked' => 'nullable|boolean',
        ]);

        $data['is_admin'] = $request->boolean('is_admin');
        $data['blocked'] = $request->boolean('blocked');

        $user->update($data);

        return Redirect::route('admin.users')->with('success', 'Usuário atualizado com sucesso.');
    }

    /**
     * Ajusta créditos do usuário (pode adicionar ou remover).
     */
    public function credit(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|integer',
        ]);

        $amount = (int) $request->input('amount');
        try {
            if ($amount > 0) {
                $user->addCredits($amount);
            } elseif ($amount < 0) {
                $user->removeCredits(abs($amount));
            }
        } catch (\Exception $e) {
            Log::error('Erro ao ajustar créditos: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Créditos ajustados com sucesso.');
    }

    /**
     * Bloqueia ou desbloqueia o usuário.
     */
    public function toggleBlock(User $user)
    {
        $user->blocked = !$user->blocked;
        $user->save();

        return back()->with('success', $user->blocked ? 'Usuário bloqueado.' : 'Usuário desbloqueado.');
    }
} 