@extends('layouts.guest-page')

@section('title', 'Termos de Serviço - AutoRecurso')
@section('meta_description', 'Termos de serviço do AutoRecurso. Leia nossas condições de uso, responsabilidades e limitações do serviço de geração de recursos de multas.')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white py-12 sm:py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Cabeçalho da página -->
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-3">
                Termos de Serviço
            </h1>
            <div class="inline-flex items-center justify-center px-4 py-2 bg-blue-50 rounded-full text-sm text-gray-600">
                <i class="fas fa-clock mr-2"></i>
                <span>Última atualização: 01/04/2025</span>
            </div>
        </div>

        <!-- Conteúdo principal -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 sm:p-8">
                <!-- 1. Aceitação dos Termos -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-check text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">1. Aceitação dos Termos</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600 leading-relaxed">
                            Ao acessar e usar o AutoRecurso, você concorda em cumprir estes termos de serviço. Se você não concordar com qualquer parte destes termos, não poderá usar nosso serviço.
                        </p>
                    </div>
                </section>

                <!-- 2. Responsabilidade do Usuário -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-user text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">2. Responsabilidade do Usuário</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600 mb-3">O usuário é inteiramente responsável por:</p>
                        <ul class="bg-gray-50 rounded-lg p-4 sm:p-6 space-y-3 border border-gray-100">
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Fornecer informações precisas e verdadeiras para a geração do recurso</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Verificar a veracidade de todas as informações fornecidas</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Enviar o recurso gerado aos órgãos competentes</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Acompanhar o processo de análise do recurso</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Arcar com eventuais custos relacionados ao processo de recurso</span>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- 3. Limitação de Responsabilidade -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">3. Limitação de Responsabilidade</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600 mb-3">O AutoRecurso atua apenas como uma ferramenta de auxílio na geração de recursos, e:</p>
                        <ul class="bg-gray-50 rounded-lg p-4 sm:p-6 space-y-3 border border-gray-100">
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Não garante a aprovação do recurso</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Não se responsabiliza pela veracidade das informações fornecidas pelo usuário</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Não se responsabiliza por eventuais prejuízos decorrentes do uso do serviço</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Não garante resultados específicos no processo de recurso</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Não se responsabiliza por prazos ou procedimentos específicos de cada órgão</span>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- 4. Uso do Serviço -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-cog text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">4. Uso do Serviço</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600 mb-3">O AutoRecurso:</p>
                        <ul class="bg-gray-50 rounded-lg p-4 sm:p-6 space-y-3 border border-gray-100">
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Utiliza inteligência artificial para gerar recursos baseados nas informações fornecidas</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Não faz validação ou verificação das informações fornecidas</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Não se responsabiliza pela adequação do recurso às leis específicas</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Recomenda a revisão do recurso por um profissional qualificado</span>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-5 h-5 bg-blue-100 rounded flex items-center justify-center mt-0.5 mr-3">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-gray-600">Não garante a conformidade com requisitos específicos de cada órgão</span>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- 5. Modificações do Serviço -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-sync text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">5. Modificações do Serviço</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600">
                            Reservamos o direito de modificar ou descontinuar o serviço a qualquer momento, com ou sem aviso prévio. 
                            Não seremos responsáveis perante você ou terceiros por qualquer modificação, suspensão ou descontinuação do serviço.
                        </p>
                    </div>
                </section>

                <!-- 6. Contato -->
                <section class="mb-8 sm:mb-10">
                    <div class="flex items-start sm:items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-envelope text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 pt-1">6. Contato</h2>
                    </div>
                    <div class="ml-14 sm:ml-16">
                        <p class="text-gray-600">
                            Para questões relacionadas a estes termos de serviço, entre em contato conosco através do email: 
                            <a href="mailto:contato@autorecurso.online" class="text-blue-600 hover:text-blue-800 font-medium">
                                contato@autorecurso.online
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