@extends('self-service.layout')

@section('title', 'Recurso Gerado com Sucesso - AutoRecurso')

@push('styles')
<style>
/* Animações especiais para página de sucesso */
@keyframes checkmark {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
        opacity: 1;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes confetti {
    0% {
        transform: translateY(-100vh) rotate(0deg);
        opacity: 1;
    }
    100% {
        transform: translateY(100vh) rotate(360deg);
        opacity: 0;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.success-checkmark {
    animation: checkmark 0.8s ease-in-out;
}

.confetti-piece {
    position: absolute;
    width: 10px;
    height: 10px;
    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
    animation: confetti 3s linear infinite;
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

.success-bg {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.success-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.pulse-success {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.success-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
}

.next-steps {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    border-left: 4px solid #3b82f6;
}

.whatsapp-float {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    background: #25d366;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
    z-index: 1000;
    animation: pulse 2s infinite;
}

.whatsapp-float:hover {
    transform: scale(1.1);
}

.timeline {
    position: relative;
    padding-left: 3rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #3b82f6, #10b981);
}

.timeline-item {
    position: relative;
    padding-bottom: 2rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -2.5rem;
    top: 0.5rem;
    width: 12px;
    height: 12px;
    background: #3b82f6;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 3px #3b82f6;
}

.timeline-item.completed::before {
    background: #10b981;
    box-shadow: 0 0 0 3px #10b981;
}

.social-proof {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #0ea5e9;
}

.rating-stars {
    color: #fbbf24;
    font-size: 1.2rem;
}

.process-step {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.process-step:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

.expectation-card {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 1px solid #f59e0b;
}

.benefit-highlight {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border: 1px solid #10b981;
}

.success-animation {
    animation: successBounce 0.6s ease-out;
}

@keyframes successBounce {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}

.step-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.step-card:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(59,130,246,0.1);
}

.step-number {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.floating-card {
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.checklist-item {
    transition: all 0.3s ease;
}

.checklist-item:hover {
    background-color: #f8fafc;
    padding-left: 1.5rem;
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-indigo-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        
        <!-- Header de Sucesso -->
        <div class="text-center mb-12">
            <div class="success-animation mb-6">
                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                🎉 Recurso Gerado com Sucesso!
            </h1>
            
            <p class="text-xl text-gray-600 mb-6">
                Seu recurso personalizado foi criado pela nossa <strong>Inteligência Artificial</strong> especializada
            </p>
            
            <div class="floating-card bg-white p-6 rounded-2xl shadow-lg inline-block">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">📧 Verifique seu email agora!</h3>
                <p class="text-gray-600">Enviamos todas as instruções e o PDF do recurso</p>
            </div>
        </div>

        <!-- Cards informativos -->
        <div class="grid md:grid-cols-2 gap-8 mb-12">
            <!-- Card do que foi enviado -->
            <div class="bg-white p-8 rounded-2xl shadow-lg">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">O que foi enviado?</h3>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✅</span>
                        <span class="text-gray-700"><strong>PDF do recurso</strong> pronto para imprimir</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✅</span>
                        <span class="text-gray-700"><strong>Instruções detalhadas</strong> de protocolamento</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✅</span>
                        <span class="text-gray-700"><strong>Lista completa</strong> de documentos necessários</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✅</span>
                        <span class="text-gray-700"><strong>Endereços dos órgãos</strong> competentes</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✅</span>
                        <span class="text-gray-700"><strong>Dicas especiais</strong> para maximizar aprovação</span>
                    </div>
                </div>
            </div>

            <!-- Card de tempo restante -->
            <div class="bg-gradient-to-br from-orange-50 to-red-50 p-8 rounded-2xl shadow-lg border-2 border-orange-200">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">⏰ Prazo Importante</h3>
                </div>
                
                <div class="text-center">
                    <p class="text-2xl font-bold text-orange-600 mb-2">30 DIAS</p>
                    <p class="text-gray-700 mb-4">para protocolar seu recurso a partir da data de notificação da multa</p>
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <p class="text-sm text-gray-600"><strong>Dica:</strong> Não deixe para a última hora! Protocole assim que possível.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximos passos -->
        <div class="bg-white p-8 rounded-2xl shadow-lg mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">
                🚀 Próximos Passos - Siga Esta Ordem
            </h2>
            
            <div class="grid md:grid-cols-3 gap-6">
                <!-- Passo 1 -->
                <div class="step-card bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="step-number w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">1</div>
                        <h3 class="font-bold text-gray-900">Verifique o Email</h3>
                    </div>
                    <p class="text-gray-700 text-sm mb-4">Acesse sua caixa de entrada e baixe o PDF do recurso. Verifique também a pasta de spam.</p>
                    <div class="text-xs text-gray-500">
                        <span class="block">📧 Pode demorar até 5 minutos</span>
                        <span>💡 Salve o PDF em local seguro</span>
                    </div>
                </div>

                <!-- Passo 2 -->
                <div class="step-card bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-xl shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="step-number w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">2</div>
                        <h3 class="font-bold text-gray-900">Prepare Documentos</h3>
                    </div>
                    <p class="text-gray-700 text-sm mb-4">Imprima o recurso, assine e separe todas as cópias dos documentos listados no email.</p>
                    <div class="text-xs text-gray-500">
                        <span class="block">📄 Use papel A4 branco</span>
                        <span>✍️ Assine com caneta azul</span>
                    </div>
                </div>

                <!-- Passo 3 -->
                <div class="step-card bg-gradient-to-br from-purple-50 to-pink-50 p-6 rounded-xl shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="step-number w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">3</div>
                        <h3 class="font-bold text-gray-900">Protocole o Recurso</h3>
                    </div>
                    <p class="text-gray-700 text-sm mb-4">Vá até o órgão competente ou protocole online seguindo as instruções detalhadas do email.</p>
                    <div class="text-xs text-gray-500">
                        <span class="block">🏢 JARI ou Detran</span>
                        <span>📱 Ou protocolo online</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checklist final -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-8 rounded-2xl shadow-lg mb-12">
            <h2 class="text-xl font-bold text-gray-900 mb-6 text-center">
                ✅ Checklist Final - Antes de Protocolar
            </h2>
            
            <div class="max-w-2xl mx-auto space-y-3">
                <div class="checklist-item p-3 rounded-lg cursor-pointer flex items-center">
                    <input type="checkbox" class="mr-4 w-5 h-5 text-blue-600 rounded">
                    <span class="text-gray-700">Baixei e imprimi o PDF do recurso</span>
                </div>
                <div class="checklist-item p-3 rounded-lg cursor-pointer flex items-center">
                    <input type="checkbox" class="mr-4 w-5 h-5 text-blue-600 rounded">
                    <span class="text-gray-700">Assinei o recurso com caneta azul</span>
                </div>
                <div class="checklist-item p-3 rounded-lg cursor-pointer flex items-center">
                    <input type="checkbox" class="mr-4 w-5 h-5 text-blue-600 rounded">
                    <span class="text-gray-700">Separei todas as cópias dos documentos</span>
                </div>
                <div class="checklist-item p-3 rounded-lg cursor-pointer flex items-center">
                    <input type="checkbox" class="mr-4 w-5 h-5 text-blue-600 rounded">
                    <span class="text-gray-700">Verifiquei endereço e horário do órgão</span>
                </div>
                <div class="checklist-item p-3 rounded-lg cursor-pointer flex items-center">
                    <input type="checkbox" class="mr-4 w-5 h-5 text-blue-600 rounded">
                    <span class="text-gray-700">Confirmei que estou dentro do prazo de 30 dias</span>
                </div>
            </div>
        </div>

        <!-- Estatísticas de sucesso -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-8 rounded-2xl shadow-lg text-white text-center mb-12">
            <h2 class="text-2xl font-bold mb-6">📊 Suas Chances de Sucesso</h2>
            
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <div class="text-3xl font-bold mb-2">95%</div>
                    <div class="text-blue-100">Taxa de Aprovação</div>
                </div>
                <div>
                    <div class="text-3xl font-bold mb-2">10.000+</div>
                    <div class="text-blue-100">Recursos Aprovados</div>
                </div>
                <div>
                    <div class="text-3xl font-bold mb-2">30 dias</div>
                    <div class="text-blue-100">Resposta Média</div>
                </div>
            </div>
            
            <p class="mt-6 text-blue-100">
                Recursos gerados com nossa IA têm <strong>95% de chance de aprovação</strong>
            </p>
        </div>

        <!-- Suporte -->
        <div class="bg-white p-8 rounded-2xl shadow-lg text-center">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                🆘 Precisa de Ajuda?
            </h2>
            
            <p class="text-gray-600 mb-6">
                Nossa equipe está pronta para te ajudar em qualquer etapa do processo
            </p>
            
            <div class="grid md:grid-cols-3 gap-4">
                <a href="mailto:suporte@autorecurso.com.br" class="flex items-center justify-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-blue-700 font-medium">Email</span>
                </a>
                
                <a href="https://wa.me/5511999999999" class="flex items-center justify-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.309"/>
                    </svg>
                    <span class="text-green-700 font-medium">WhatsApp</span>
                </a>
                
                <div class="flex items-center justify-center p-4 bg-gray-50 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-gray-700 font-medium">Seg-Sex 8h-18h</span>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
// Animação dos checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.checklist-item input[type="checkbox"]');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const item = this.closest('.checklist-item');
            if (this.checked) {
                item.classList.add('bg-green-50');
                item.querySelector('span').classList.add('line-through', 'text-green-600');
            } else {
                item.classList.remove('bg-green-50');
                item.querySelector('span').classList.remove('line-through', 'text-green-600');
            }
        });
    });
});
</script>
@endpush
@endsection 