<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function edit(User $user): View
    {
        return view('admin.user_edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email',
            'is_admin' => 'nullable|boolean',
            'blocked' => 'nullable|boolean',
            'subscription_active' => 'nullable|boolean',
            'subscription_ends_at' => 'nullable|date',
        ]);

        if ($request->has('name')) {
            $user->name = $validated['name'];
        }
        if ($request->has('email')) {
            $user->email = $validated['email'];
        }
        
        // Atualiza is_admin apenas se o campo estiver presente no request
        if ($request->has('is_admin')) {
            $user->is_admin = (bool) $request->boolean('is_admin');
        }
        
        // Atualiza blocked apenas se o campo estiver presente no request
        if ($request->has('blocked')) {
            $user->blocked = (bool) $request->boolean('blocked');
        }
        
        // Atualiza subscription_active apenas se o campo estiver presente no request
        if ($request->has('subscription_active')) {
            $user->subscription_active = (bool) $request->boolean('subscription_active');
        } else {
            // Se o checkbox não vier marcado, definir como false
            $user->subscription_active = false;
        }
        
        if ($request->filled('subscription_ends_at')) {
            $user->subscription_ends_at = $request->date('subscription_ends_at');
        } else {
            $user->subscription_ends_at = null;
        }
        
        $user->save();

        return redirect()->route('admin.users')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function toggleBlock(User $user): RedirectResponse
    {
        $user->blocked = !$user->blocked;
        $user->save();
        return back()->with('success', $user->blocked ? 'Usuário bloqueado.' : 'Usuário desbloqueado.');
    }

}


