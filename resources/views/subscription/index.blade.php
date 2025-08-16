@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-4">Planos de Assinatura</h1>
    <p class="text-gray-600 mb-6">Assine o AutoRecurso IA. Preço base R$ 150/mês, com descontos progressivos para planos mais longos.</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $base = config('subscription.pricing.base_monthly');
            $discounts = config('subscription.pricing.discounts');
        @endphp
        @foreach($plans as $plan)
            @php
                $months = $plan['months'];
                $discount = 0.0;
                foreach ($discounts as $threshold => $percent) {
                    if ($months >= (int)$threshold) { $discount = max($discount, (float)$percent); }
                }
                $perMonth = round($base * (1 - $discount), 2);
                $total = round($perMonth * $months, 2);
            @endphp
            <div class="border rounded-xl p-5 bg-white shadow">
                <h2 class="text-xl font-semibold mb-2">{{ $months }} mês{{ $months>1?'es':'' }}</h2>
                <div class="text-3xl font-bold mb-1">R$ {{ number_format($perMonth, 2, ',', '.') }} <span class="text-base text-gray-500">/mês</span></div>
                <div class="text-gray-600 mb-4">Total: R$ {{ number_format($total, 2, ',', '.') }} {!! $discount>0 ? '<span class="ml-2 text-green-600">('.(int)($discount*100).'% OFF)</span>' : '' !!}</div>
                <form action="{{ route('subscription.subscribe') }}" method="POST">
                    @csrf
                    <input type="hidden" name="months" value="{{ $months }}">
                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-2 font-semibold">Assinar</button>
                </form>
            </div>
        @endforeach
    </div>

    @if($user && $user->subscription_active)
        <div class="mt-8 p-4 bg-green-50 border border-green-200 rounded">
            <div class="font-semibold text-green-800">Sua assinatura está ativa</div>
            <div class="text-green-700 text-sm">Válida até: {{ optional($user->subscription_ends_at)->format('d/m/Y') }}</div>
        </div>
    @endif
</div>
@endsection


