<x-guest-layout>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Termos de Serviço</h1>
            <p class="mt-2 text-gray-600">Última atualização: {{ date('d/m/Y') }}</p>
        </div>

        <div class="prose prose-blue max-w-none">
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">1. Aceitação dos Termos</h2>
                <p class="text-gray-600">
                    Ao acessar e usar o AutoRecurso, você concorda em cumprir estes termos de serviço. Se você não concordar com qualquer parte destes termos, não poderá usar nosso serviço.
                </p>
            </div>

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">2. Responsabilidade do Usuário</h2>
                <p class="text-gray-600 mb-4">
                    O usuário é inteiramente responsável por:
                </p>
                <ul class="list-disc pl-6 text-gray-600 space-y-2">
                    <li>Fornecer informações precisas e verdadeiras para a geração do recurso</li>
                    <li>Verificar a veracidade de todas as informações fornecidas</li>
                    <li>Enviar o recurso gerado aos órgãos competentes</li>
                    <li>Acompanhar o processo de análise do recurso</li>
                    <li>Arcar com eventuais custos relacionados ao processo de recurso</li>
                </ul>
            </div>

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">3. Limitação de Responsabilidade</h2>
                <p class="text-gray-600 mb-4">
                    O AutoRecurso atua apenas como uma ferramenta de auxílio na geração de recursos, e:
                </p>
                <ul class="list-disc pl-6 text-gray-600 space-y-2">
                    <li>Não garante a aprovação do recurso</li>
                    <li>Não se responsabiliza pela veracidade das informações fornecidas pelo usuário</li>
                    <li>Não se responsabiliza por eventuais prejuízos decorrentes do uso do serviço</li>
                    <li>Não garante resultados específicos no processo de recurso</li>
                    <li>Não se responsabiliza por prazos ou procedimentos específicos de cada órgão</li>
                </ul>
            </div>

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">4. Uso do Serviço</h2>
                <p class="text-gray-600 mb-4">
                    O AutoRecurso:
                </p>
                <ul class="list-disc pl-6 text-gray-600 space-y-2">
                    <li>Utiliza inteligência artificial para gerar recursos baseados nas informações fornecidas</li>
                    <li>Não faz validação ou verificação das informações fornecidas</li>
                    <li>Não se responsabiliza pela adequação do recurso às leis específicas</li>
                    <li>Recomenda a revisão do recurso por um profissional qualificado</li>
                    <li>Não garante a conformidade com requisitos específicos de cada órgão</li>
                </ul>
            </div>

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">5. Modificações do Serviço</h2>
                <p class="text-gray-600">
                    Reservamos o direito de modificar ou descontinuar o serviço a qualquer momento, com ou sem aviso prévio. Não seremos responsáveis perante você ou terceiros por qualquer modificação, suspensão ou descontinuação do serviço.
                </p>
            </div>

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">6. Contato</h2>
                <p class="text-gray-600">
                    Para questões relacionadas a estes termos de serviço, entre em contato conosco através do email: contato@autorecurso.online
                </p>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-4 border-2 border-blue-600 text-base font-bold rounded-lg text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:from-blue-600 hover:via-blue-700 hover:to-blue-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <i class="fas fa-arrow-left mr-3 text-lg"></i>
                Voltar para o Login
            </a>
        </div>
    </div>
</x-guest-layout> 