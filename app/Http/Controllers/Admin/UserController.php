<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CreditService;
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
        ]);

        if ($request->has('name')) {
            $user->name = $validated['name'];
        }
        if ($request->has('email')) {
            $user->email = $validated['email'];
        }
        $user->is_admin = (bool) $request->boolean('is_admin');
        $user->blocked = (bool) $request->boolean('blocked');
        $user->save();

        return redirect()->route('admin.users')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function toggleBlock(User $user): RedirectResponse
    {
        $user->blocked = !$user->blocked;
        $user->save();
        return back()->with('success', $user->blocked ? 'Usuário bloqueado.' : 'Usuário desbloqueado.');
    }

    public function credit(Request $request, User $user, CreditService $creditService): RedirectResponse
    {
        $data = $request->validate([
            'amount' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:255',
        ]);

        try {
            $creditService->adjustCredits($user, (int) $data['amount'], $data['reason']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Créditos ajustados com sucesso.');
    }
}


