<x-guest-layout>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-center mb-8">
                    <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-user-shield text-blue-600 text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">Solicitação de Dados Pessoais</h1>
                    <p class="text-gray-600">Exercite seus direitos conforme a Lei Geral de Proteção de Dados (LGPD)</p>
                </div>

                <div class="mb-8">
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-blue-800 mb-4">Seus Direitos sob a LGPD</h3>
                        <ul class="space-y-2 text-blue-700">
                            <li class="flex items-start">
                                <i class="fas fa-check text-blue-600 mt-1 mr-2"></i>
                                <span><strong>Confirmação e acesso:</strong> Saber se processamos seus dados e acessá-los</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-blue-600 mt-1 mr-2"></i>
                                <span><strong>Correção:</strong> Corrigir dados incompletos, inexatos ou desatualizados</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-blue-600 mt-1 mr-2"></i>
                                <span><strong>Portabilidade:</strong> Receber seus dados em formato estruturado</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-blue-600 mt-1 mr-2"></i>
                                <span><strong>Eliminação:</strong> Excluir dados tratados com base no consentimento</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-blue-600 mt-1 mr-2"></i>
                                <span><strong>Revogação do consentimento:</strong> Retirar consentimento a qualquer momento</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="request_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Solicitação *
                        </label>
                        <select id="request_type" name="request_type" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecione o tipo de solicitação</option>
                            <option value="access">Acesso aos meus dados pessoais</option>
                            <option value="correction">Correção de dados incorretos</option>
                            <option value="portability">Portabilidade dos dados</option>
                            <option value="deletion">Exclusão dos meus dados</option>
                            <option value="consent_revocation">Revogação de consentimento</option>
                            <option value="information">Informações sobre compartilhamento</option>
                            <option value="other">Outro (especificar na mensagem)</option>
                        </select>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome Completo *
                        </label>
                        <input type="text" id="name" name="name" required 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Seu nome completo">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            E-mail *
                        </label>
                        <input type="email" id="email" name="email" required 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="seu@email.com">
                        <p class="mt-1 text-sm text-gray-500">Use o mesmo e-mail cadastrado em sua conta</p>
                    </div>

                    <div>
                        <label for="cpf" class="block text-sm font-medium text-gray-700 mb-2">
                            CPF *
                        </label>
                        <input type="text" id="cpf" name="cpf" required 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="000.000.000-00">
                        <p class="mt-1 text-sm text-gray-500">Para verificação de identidade</p>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Telefone
                        </label>
                        <input type="tel" id="phone" name="phone" 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="(11) 99999-9999">
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Detalhes da Solicitação *
                        </label>
                        <textarea id="message" name="message" rows="5" required 
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Descreva detalhadamente sua solicitação. Seja específico sobre quais dados você quer acessar, corrigir ou excluir."></textarea>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-yellow-800">Verificação de Identidade</h4>
                                <p class="mt-1 text-sm text-yellow-700">
                                    Para proteger sua privacidade, podemos solicitar documentos adicionais para verificar sua identidade antes de processar sua solicitação.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <input type="checkbox" id="consent" name="consent" required 
                               class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="consent" class="ml-2 text-sm text-gray-700">
                            Declaro que as informações fornecidas são verdadeiras e autorizo o processamento desta solicitação conforme a 
                            <a href="{{ route('legal.privacy') }}" class="text-blue-600 hover:underline" target="_blank">Política de Privacidade</a>. *
                        </label>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Enviar Solicitação
                        </button>
                    </div>
                </form>

                <div class="mt-8 bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações Importantes</h3>
                    <div class="space-y-3 text-sm text-gray-700">
                        <div class="flex items-start">
                            <i class="fas fa-clock text-gray-400 mt-1 mr-2"></i>
                            <span><strong>Prazo de resposta:</strong> Até 15 dias úteis conforme a LGPD</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-shield-alt text-gray-400 mt-1 mr-2"></i>
                            <span><strong>Segurança:</strong> Todas as solicitações são processadas de forma segura e confidencial</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-envelope text-gray-400 mt-1 mr-2"></i>
                            <span><strong>Resposta:</strong> Você receberá uma resposta no e-mail informado</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-id-card text-gray-400 mt-1 mr-2"></i>
                            <span><strong>Verificação:</strong> Documentos de identificação podem ser solicitados</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">
                        Dúvidas? Entre em contato: 
                        <a href="mailto:privacidade@autorecurso.online" class="text-blue-600 hover:underline">privacidade@autorecurso.online</a>
                    </p>
                </div>

                <div class="mt-8 text-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para CPF
    const cpfInput = document.getElementById('cpf');
    cpfInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    });

    // Máscara para telefone
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d)(\d{4})$/, '$1-$2');
        e.target.value = value;
    });
});
</script> 