@extends('self-service.layout')

@section('title', 'Teste Landing Page')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 flex items-center justify-center">
    <div class="max-w-2xl mx-auto text-center p-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-6">
            🎉 LANDING PAGE FUNCIONANDO!
        </h1>
        
        <p class="text-xl text-gray-600 mb-8">
            Se você está vendo esta mensagem, a rota está funcionando corretamente.
        </p>
        
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-2xl font-semibold mb-4">Teste de Navegação</h2>
            <div class="space-y-4">
                <a href="{{ route('cliente.wizard') }}" 
                   class="block w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors">
                    Ir para o Wizard
                </a>
                
                <a href="{{ route('cliente.success') }}" 
                   class="block w-full bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition-colors">
                    Ir para Página de Sucesso
                </a>
            </div>
        </div>
        
        <div class="bg-yellow-100 border border-yellow-400 rounded-lg p-4">
            <p class="text-sm text-yellow-800">
                <strong>Próximo passo:</strong> Agora vamos carregar a landing page completa!
            </p>
        </div>
    </div>
</div>
@endsection 