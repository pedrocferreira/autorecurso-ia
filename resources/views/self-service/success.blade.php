@extends('self-service.layout')

@section('title', 'Sucesso! - AutoRecurso')

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
</style>
@endpush

@section('content')
<div class="min-h-screen success-bg relative overflow-hidden" x-data="successPage()">
    <!-- Confetti Animation -->
    <div class="fixed inset-0 pointer-events-none">
        <template x-for="i in 50" :key="i">
            <div class="confetti-piece" 
                 :style="`left: ${Math.random() * 100}%; animation-delay: ${Math.random() * 3}s; background: ${['#ff6b6b', '#4ecdc4', '#45b7d1', '#f9ca24', '#f0932b', '#eb4d4b', '#6c5ce7'][Math.floor(Math.random() * 7)]}`">
            </div>
        </template>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-20">
        <!-- Success Message -->
        <div class="success-card rounded-3xl p-8 text-center mb-12 fade-in-up">
            <div class="success-icon w-24 h-24 success-checkmark">
                <i class="fas fa-check text-white text-4xl"></i>
            </div>
            
            <h1 class="text-4xl font-bold gradient-text mb-4">
                Parabéns! Sua Solicitação foi Enviada!
            </h1>
            
            <p class="text-xl text-gray-600 mb-8">
                Recebemos sua multa e nossa <strong>IA especializada</strong> já começou a análise. 
                Você receberá o resultado em até <strong>48 horas</strong>.
            </p>

            <!-- Protocolo -->
            <div class="bg-blue-50 p-6 rounded-2xl mb-8 inline-block">
                <div class="flex items-center justify-center gap-3">
                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                    <div>
                        <p class="text-sm text-gray-600">Número do Protocolo</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="protocolNumber"></p>
                    </div>
                </div>
            </div>

            <!-- Social Proof -->
            <div class="social-proof p-6 rounded-2xl">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="rating-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <span class="text-lg font-semibold">4.9/5</span>
                </div>
                <p class="text-sm text-gray-600">
                    <strong>12.543 multas</strong> anuladas com <strong>85% de taxa de sucesso</strong>
                </p>
            </div>
        </div>

        <!-- O que acontece agora -->
        <div class="bg-white rounded-3xl p-8 mb-12 fade-in-up">
            <h2 class="text-3xl font-bold text-center mb-8">O que acontece agora?</h2>
            
            <div class="timeline">
                <div class="timeline-item completed">
                    <div class="flex items-start gap-4">
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Multa Recebida</h3>
                            <p class="text-gray-600">Sua multa foi recebida e está em nossa base de dados</p>
                            <p class="text-sm text-green-600 font-medium">✓ Concluído</p>
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-100 p-3 rounded-full pulse-success">
                            <i class="fas fa-robot text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Análise por IA</h3>
                            <p class="text-gray-600">Nossa IA está analisando 150+ critérios técnicos e jurídicos</p>
                            <p class="text-sm text-blue-600 font-medium">⏳ Em andamento</p>
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="flex items-start gap-4">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <i class="fas fa-gavel text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Revisão Jurídica</h3>
                            <p class="text-gray-600">Advogados especialistas validarão a análise da IA</p>
                            <p class="text-sm text-gray-500">⏳ Próximo passo</p>
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="flex items-start gap-4">
                        <div class="bg-orange-100 p-3 rounded-full">
                            <i class="fas fa-paper-plane text-orange-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Protocolo do Recurso</h3>
                            <p class="text-gray-600">Envio automático do recurso para o órgão competente</p>
                            <p class="text-sm text-gray-500">⏳ Em breve</p>
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="flex items-start gap-4">
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-trophy text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Resultado</h3>
                            <p class="text-gray-600">Você receberá o resultado em até 48 horas</p>
                            <p class="text-sm text-gray-500">⏳ Até 48h</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expectativas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <div class="expectation-card rounded-2xl p-8">
                <div class="flex items-center gap-4 mb-4">
                    <i class="fas fa-clock text-2xl text-orange-600"></i>
                    <h3 class="text-xl font-bold">Quando vou receber o resultado?</h3>
                </div>
                <p class="text-gray-700 mb-4">
                    Você receberá um e-mail com o resultado da análise em <strong>até 48 horas</strong>. 
                    Se for identificada uma possibilidade de anulação, protocolamos o recurso automaticamente.
                </p>
                <div class="flex items-center gap-2 text-sm">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span class="text-gray-600">Média atual: 36 horas</span>
                </div>
            </div>

            <div class="benefit-highlight rounded-2xl p-8">
                <div class="flex items-center gap-4 mb-4">
                    <i class="fas fa-shield-check text-2xl text-green-600"></i>
                    <h3 class="text-xl font-bold">Lembre-se da nossa garantia</h3>
                </div>
                <p class="text-gray-700 mb-4">
                    Você só paga se conseguirmos anular sua multa. Se o recurso não for aceito, 
                    você não paga absolutamente nada.
                </p>
                <div class="flex items-center gap-2 text-sm">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span class="text-gray-600">Garantia 100% sem risco</span>
                </div>
            </div>
        </div>

        <!-- Próximos Passos -->
        <div class="bg-white rounded-3xl p-8 mb-12 fade-in-up">
            <h2 class="text-3xl font-bold text-center mb-8">Enquanto isso, você pode:</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="process-step text-center">
                    <i class="fas fa-bell text-3xl text-blue-600 mb-4"></i>
                    <h3 class="text-lg font-semibold mb-2">Fique Atento ao E-mail</h3>
                    <p class="text-gray-600 text-sm">
                        Verifique sua caixa de entrada e spam. Enviaremos todas as atualizações por e-mail.
                    </p>
                </div>

                <div class="process-step text-center">
                    <i class="fas fa-share-alt text-3xl text-green-600 mb-4"></i>
                    <h3 class="text-lg font-semibold mb-2">Indique um Amigo</h3>
                    <p class="text-gray-600 text-sm">
                        Compartilhe com amigos que também têm multas. Quanto mais pessoas ajudarmos, melhor!
                    </p>
                </div>

                <div class="process-step text-center">
                    <i class="fas fa-headset text-3xl text-purple-600 mb-4"></i>
                    <h3 class="text-lg font-semibold mb-2">Suporte Disponível</h3>
                    <p class="text-gray-600 text-sm">
                        Dúvidas? Nossa equipe está disponível 24/7 para ajudar você.
                    </p>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl p-8 text-white text-center mb-12">
            <h2 class="text-3xl font-bold mb-8">Resultados que Comprovam</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="text-4xl font-bold mb-2">85%</div>
                    <p class="text-blue-100">Taxa de Sucesso</p>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">12.543</div>
                    <p class="text-blue-100">Multas Anuladas</p>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">R$ 2.8M</div>
                    <p class="text-blue-100">Economizados</p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center">
            <h2 class="text-2xl font-bold text-white mb-4">Tem mais multas para anular?</h2>
            <p class="text-blue-100 mb-6">
                Aproveite e analise todas as suas multas de uma vez!
            </p>
            <a href="{{ route('cliente.wizard') }}" class="bg-white text-blue-600 px-8 py-4 rounded-2xl font-semibold hover:bg-blue-50 transition-all duration-300 inline-flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Analisar Outra Multa
            </a>
        </div>
    </div>

    <!-- WhatsApp Float -->
    <a href="https://wa.me/5511999999999?text=Olá! Acabei de enviar uma multa para análise e gostaria de tirar algumas dúvidas." 
       target="_blank" 
       class="whatsapp-float">
        <i class="fab fa-whatsapp text-white text-2xl"></i>
    </a>
</div>
@endsection

@push('scripts')
<script>
function successPage() {
    return {
        protocolNumber: '#AR' + Math.floor(Math.random() * 1000000).toString().padStart(6, '0'),
        
        init() {
            // Confetti animation
            this.createConfetti();
            
            // Auto-scroll to show all content
            setTimeout(() => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }, 1000);
        },
        
        createConfetti() {
            // Additional confetti logic if needed
            console.log('Confetti created!');
        }
    }
}

// Auto-hide loading and show success
document.addEventListener('DOMContentLoaded', function() {
    // Track success conversion
    console.log('Success page loaded - conversion completed');
    
    // You can add analytics tracking here
    // Example: gtag('event', 'conversion', { send_to: 'AW-XXXXXXXXX/XXXXXX' });
});
</script>
@endpush 