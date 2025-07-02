<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pagamento PIX') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header do Pagamento -->
                    <div class="text-center mb-8">
                        <div class="flex items-center justify-center mb-4">
                            <i class="fas fa-qrcode text-4xl text-green-600 mr-3"></i>
                            <h1 class="text-3xl font-bold text-gray-800">Pagamento PIX</h1>
                        </div>
                        <p class="text-gray-600">
                            {{ $credits }} créditos por R$ {{ number_format($amount, 2, ',', '.') }}
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Pagamento PIX -->
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <h3 class="text-lg font-semibold mb-4 text-gray-800">
                                <i class="fas fa-credit-card mr-2"></i>
                                Pagar com PIX
                            </h3>
                            
                            <div class="bg-white p-6 rounded-lg mb-4">
                                <div class="mb-6">
                                    <h4 class="text-gray-700 mb-2">QR Code PIX</h4>
                                    <div class="flex justify-center">
                                        <img src="{{ $qr_code }}" 
                                             alt="QR Code PIX" 
                                             class="w-48 h-48 border rounded-lg p-2">
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <h4 class="text-gray-700 mb-2">Código PIX Copia e Cola</h4>
                                    <div class="relative">
                                        <input type="text" 
                                               value="{{ $pix_code }}" 
                                               readonly
                                               class="w-full p-3 border rounded-lg bg-gray-50 font-mono text-sm"
                                               id="pixCode"
                                               onclick="this.select()">
                                        <button onclick="copyPixCode()" 
                                                class="absolute right-2 top-2 bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>

                                <p class="text-gray-700 mb-4">
                                    Escaneie o QR Code ou copie o código PIX para pagar
                                </p>
                            </div>
                            
                            <p class="text-sm text-gray-600">
                                O pagamento será confirmado automaticamente em alguns minutos
                            </p>
                        </div>

                        <!-- Informações do Pagamento -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Detalhes do Pagamento
                            </h3>
                            
                            <div class="bg-white border rounded-lg p-4 space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Valor:</span>
                                    <span class="font-semibold">R$ {{ number_format($amount, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Créditos:</span>
                                    <span class="font-semibold">{{ $credits }} créditos</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">ID da Transação:</span>
                                    <span class="font-mono text-sm">{{ $billing_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Método:</span>
                                    <span class="font-semibold text-green-600">PIX</span>
                                </div>
                            </div>
                            
                            @if($payment_url && $payment_url !== '#')
                                <div class="mt-4">
                                    <a href="{{ $payment_url }}" 
                                       target="_blank"
                                       class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                                        <i class="fas fa-external-link-alt mr-2"></i>
                                        Abrir Página de Pagamento
                                    </a>
                                </div>
                            @endif
                            
                            <p class="text-sm text-gray-600 mt-3">
                                Você pode pagar usando o QR Code acima ou abrindo a página de pagamento
                            </p>
                        </div>
                    </div>

                    <!-- Status do Pagamento -->
                    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-blue-800">
                                <i class="fas fa-clock mr-2"></i>
                                Status do Pagamento
                            </h3>
                            <div id="statusBadge" class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>
                                Aguardando Pagamento
                            </div>
                        </div>
                        
                        <div id="statusMessage" class="text-blue-700">
                            Aguardando confirmação do pagamento PIX...
                        </div>
                        
                        <div class="mt-4 flex items-center text-sm text-blue-600">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Verificamos automaticamente o status do seu pagamento</span>
                        </div>
                    </div>

                    <!-- Informações Importantes -->
                    <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-3">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Informações Importantes
                        </h3>
                        <ul class="text-yellow-700 space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle mr-2 mt-1 text-yellow-600"></i>
                                O PIX é válido por <strong>1 hora</strong>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle mr-2 mt-1 text-yellow-600"></i>
                                Após o pagamento, os créditos são adicionados automaticamente
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle mr-2 mt-1 text-yellow-600"></i>
                                Se houver problemas, entre em contato conosco
                            </li>
                        </ul>
                    </div>

                    <!-- Ações -->
                    <div class="mt-8 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('credits.packages') }}" 
                           class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Voltar aos Pacotes
                        </a>
                        
                        <button 
                            onclick="checkPaymentStatus()"
                            id="checkStatusBtn"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Verificar Pagamento
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        const transactionId = {{ $transaction->id }};
        let checkInterval;

        // Função para copiar link de pagamento
        function copyPaymentUrl() {
            const paymentUrl = '{{ $payment_url ?? "" }}';
            
            navigator.clipboard.writeText(paymentUrl).then(function() {
                // Feedback visual
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check mr-2"></i>Copiado!';
                button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                button.classList.add('bg-green-600');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('bg-green-600');
                    button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }, 2000);
            }).catch(err => {
                console.error('Erro ao copiar:', err);
                alert('Erro ao copiar o link. Tente novamente.');
            });
        }

        // Função para verificar status do pagamento
        function checkPaymentStatus() {
            const button = document.getElementById('checkStatusBtn');
            const originalContent = button.innerHTML;
            
            // Mostrar loading
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Verificando...';
            button.disabled = true;

            fetch('{{ route("credits.pix.status") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    transaction_id: transactionId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updatePaymentStatus(data.status, data.message);
                    
                    if (data.status === 'paid') {
                        // Pagamento confirmado - redirecionar após 3 segundos
                        setTimeout(() => {
                            window.location.href = '{{ route("credits.packages") }}?payment=success';
                        }, 3000);
                    }
                } else {
                    console.error('Erro ao verificar status:', data.error);
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
            })
            .finally(() => {
                // Restaurar botão
                button.innerHTML = originalContent;
                button.disabled = false;
            });
        }

        // Função para atualizar status na tela
        function updatePaymentStatus(status, message) {
            const statusBadge = document.getElementById('statusBadge');
            const statusMessage = document.getElementById('statusMessage');
            
            statusMessage.textContent = message;
            
            // Atualizar badge conforme status
            statusBadge.className = 'px-3 py-1 rounded-full text-sm font-semibold ';
            
            switch(status) {
                case 'paid':
                    statusBadge.className += 'bg-green-100 text-green-800';
                    statusBadge.innerHTML = '<i class="fas fa-check mr-1"></i>Pago';
                    clearInterval(checkInterval);
                    break;
                case 'expired':
                    statusBadge.className += 'bg-red-100 text-red-800';
                    statusBadge.innerHTML = '<i class="fas fa-times mr-1"></i>Expirado';
                    clearInterval(checkInterval);
                    break;
                case 'cancelled':
                    statusBadge.className += 'bg-gray-100 text-gray-800';
                    statusBadge.innerHTML = '<i class="fas fa-ban mr-1"></i>Cancelado';
                    clearInterval(checkInterval);
                    break;
                default:
                    statusBadge.className += 'bg-yellow-100 text-yellow-800';
                    statusBadge.innerHTML = '<i class="fas fa-clock mr-1"></i>Aguardando';
            }
        }

        // Verificar status automaticamente a cada 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            checkInterval = setInterval(checkPaymentStatus, 5000);
            
            // Parar após 1 hora (PIX expira)
            setTimeout(() => {
                clearInterval(checkInterval);
            }, 3600000);
        });

        // Função para copiar código PIX
        function copyPixCode() {
            const pixCode = document.getElementById('pixCode');
            pixCode.select();
            document.execCommand('copy');

            // Feedback visual
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-blue-600');
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('bg-blue-600');
                button.classList.add('bg-green-600', 'hover:bg-green-700');
            }, 2000);
        }
    </script>
</x-app-layout> 