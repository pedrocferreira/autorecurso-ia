<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = config('subscription.plans');
        $user = Auth::user();
        return view('subscription.index', compact('plans', 'user'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'months' => 'required|integer|min:1|max:24',
        ]);

        $months = (int)$request->input('months');
        $pricing = config('subscription.pricing');

        $base = $pricing['base_monthly'] ?? 150.00;
        $discounts = $pricing['discounts'] ?? [];

        $discount = 0.0;
        foreach ($discounts as $threshold => $percent) {
            if ($months >= (int)$threshold) {
                $discount = max($discount, (float)$percent);
            }
        }

        $perMonth = round($base * (1 - $discount), 2);
        $total = round($perMonth * $months, 2);

        $user = Auth::user();
        $user->subscription_active = true;
        $user->subscription_ends_at = now()->addMonths($months);
        $user->save();

        Log::info('Assinatura ativada', [
            'user' => $user->id,
            'months' => $months,
            'per_month' => $perMonth,
            'total' => $total,
            'discount' => $discount,
        ]);

        return redirect()->route('dashboard')->with('success', 'Assinatura ativada com sucesso. Válida até ' . optional($user->subscription_ends_at)->format('d/m/Y'));
    }
}


