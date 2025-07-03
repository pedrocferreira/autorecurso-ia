@extends('self-service.layout')

@section('title', 'Recurso Criado com Sucesso - AutoRecurso')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
        <!-- Success Icon -->
        <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
            <i class="fas fa-check text-green-600 text-2xl"></i>
        </div>

        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-900 mb-4">
            Recurso Criado com Sucesso! 🎉
        </h1>

        <!-- Message -->
        <p class="text-gray-600 mb-6">
            Seu recurso de multa foi gerado e estará disponível em seu e-mail em até 10 minutos.
        </p>

        <!-- Features -->
        <div class="space-y-3 mb-8 text-left">
            <div class="flex items-center">
                <i class="fas fa-envelope text-blue-500 mr-3"></i>
                <span class="text-sm text-gray-700">Recurso enviado por e-mail</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-file-pdf text-red-500 mr-3"></i>
                <span class="text-sm text-gray-700">PDF pronto para impressão</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-shield-check text-green-500 mr-3"></i>
                <span class="text-sm text-gray-700">Argumentos jurídicos fundamentados</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-clock text-purple-500 mr-3"></i>
                <span class="text-sm text-gray-700">Processamento em até 10 minutos</span>
            </div>
        </div>

        <!-- CTA Button -->
        <a href="/" class="w-full inline-block bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors">
            Voltar ao Início
        </a>

        <!-- Support -->
        <p class="text-xs text-gray-500 mt-4">
            Dúvidas? Entre em contato conosco pelo WhatsApp
        </p>
    </div>
</div>
@endsection 