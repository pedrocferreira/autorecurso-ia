@extends('layouts.guest-page')

@section('title', 'Política de Privacidade - AutoRecurso')
@section('meta_description', 'Política de privacidade do AutoRecurso. Saiba como protegemos seus dados pessoais e como utilizamos as informações coletadas em nossa plataforma.')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white py-12 sm:py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Cabeçalho da página -->
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-3">
                Política de Privacidade
            </h1>
            <div class="inline-flex items-center justify-center px-4 py-2 bg-blue-50 rounded-full text-sm text-gray-600">
                <i class="fas fa-clock mr-2"></i>
                <span>Última atualização: 01/04/2025</span>
            </div>
        </div>

        <!-- Conteúdo principal -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 sm:p-8">
                <!-- 1. Introdução -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">1. Introdução</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600 leading-relaxed">
                            A sua privacidade é importante para nós. Esta política de privacidade descreve como o AutoRecurso coleta, 
                            usa, processa e protege suas informações pessoais quando você utiliza nosso serviço.
                        </p>
                    </div>
                </section>

                <!-- 2. Informações que Coletamos -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-database text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">2. Informações que Coletamos</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600 mb-3">Coletamos os seguintes tipos de informações:</p>
                        <ul class="bg-gray-50 rounded-lg p-4 sm:p-6 space-y-3 border border-gray-100">
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Informações de cadastro (nome, email, telefone)</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Dados do veículo e multas</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Informações de pagamento</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Dados de uso da plataforma</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Cookies e dados de navegação</span>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- 3. Como Usamos suas Informações -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-cogs text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">3. Como Usamos suas Informações</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600 mb-3">Utilizamos suas informações para:</p>
                        <ul class="bg-gray-50 rounded-lg p-4 sm:p-6 space-y-3 border border-gray-100">
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Gerar recursos de multas personalizados</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Processar pagamentos</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Enviar atualizações sobre seu recurso</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Melhorar nossos serviços</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Cumprir obrigações legais</span>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- 4. Proteção de Dados -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">4. Proteção de Dados</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600 mb-3">
                            Implementamos medidas de segurança técnicas e organizacionais para proteger suas informações pessoais contra:
                        </p>
                        <ul class="bg-gray-50 rounded-lg p-4 sm:p-6 space-y-3 border border-gray-100">
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Acesso não autorizado</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Alteração indevida</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Divulgação ou destruição</span>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- 5. Compartilhamento de Dados -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-share-alt text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">5. Compartilhamento de Dados</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600 mb-3">
                            Não vendemos, alugamos ou compartilhamos suas informações pessoais com terceiros, exceto quando:
                        </p>
                        <ul class="bg-gray-50 rounded-lg p-4 sm:p-6 space-y-3 border border-gray-100">
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Necessário para processar pagamentos</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Exigido por lei</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Autorizado por você</span>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- 6. Seus Direitos -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-user-shield text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">6. Seus Direitos</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600 mb-3">Você tem direito a:</p>
                        <ul class="bg-gray-50 rounded-lg p-4 sm:p-6 space-y-3 border border-gray-100">
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Acessar seus dados pessoais</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Corrigir dados incorretos</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Solicitar a exclusão de seus dados</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Revogar consentimento</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Receber seus dados em formato estruturado</span>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- 7. Cookies -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-cookie-bite text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">7. Cookies</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600">
                            Utilizamos cookies para melhorar sua experiência de navegação. Você pode configurar seu navegador 
                            para recusar cookies, mas isso pode afetar algumas funcionalidades do site.
                        </p>
                    </div>
                </section>

                <!-- 8. Alterações na Política -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-sync text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">8. Alterações na Política</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600">
                            Podemos atualizar esta política periodicamente. Recomendamos que você revise esta página regularmente 
                            para se manter informado sobre quaisquer mudanças.
                        </p>
                    </div>
                </section>

                <!-- 9. Contato -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-envelope text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">9. Contato</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600">
                            Para questões relacionadas à privacidade de seus dados, entre em contato através do email: 
                            <a href="mailto:privacidade@autorecurso.online" class="text-blue-600 hover:text-blue-800 font-medium">
                                privacidade@autorecurso.online
                            </a>
                        </p>
                    </div>
                </section>
            </div>

            <!-- Botão de voltar -->
            <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-100">
                <a href="{{ route('welcome') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium transition-colors duration-200">
                    <i class="fas fa-arrow-left text-sm mr-2"></i>
                    <span>Voltar para a Página Inicial</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 