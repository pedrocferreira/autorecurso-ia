<x-guest-layout>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Política de Privacidade</h1>
            <p class="mt-2 text-gray-600">Última atualização: {{ date('d/m/Y') }}</p>
        </div>

        <div class="prose prose-blue max-w-none">
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">1. Coleta de Informações</h2>
                <p class="text-gray-600 mb-4">
                    Coletamos as seguintes informações:
                </p>
                <ul class="list-disc pl-6 text-gray-600 space-y-2">
                    <li>Informações pessoais (nome, email)</li>
                    <li>Dados de multas e infrações</li>
                    <li>Informações de pagamento (processadas de forma segura)</li>
                    <li>Dados de uso do sistema</li>
                    <li>Informações de recursos gerados</li>
                </ul>
            </div>

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">2. Uso das Informações</h2>
                <p class="text-gray-600 mb-4">
                    Utilizamos suas informações para:
                </p>
                <ul class="list-disc pl-6 text-gray-600 space-y-2">
                    <li>Gerar recursos de multas</li>
                    <li>Processar pagamentos</li>
                    <li>Enviar comunicações importantes</li>
                    <li>Melhorar nossos serviços</li>
                    <li>Prevenir fraudes</li>
                </ul>
            </div>

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">3. Proteção de Dados</h2>
                <p class="text-gray-600 mb-4">
                    Implementamos medidas de segurança para proteger suas informações:
                </p>
                <ul class="list-disc pl-6 text-gray-600 space-y-2">
                    <li>Criptografia de dados sensíveis</li>
                    <li>Firewalls e proteção contra ataques</li>
                    <li>Acesso restrito a informações pessoais</li>
                    <li>Backups regulares</li>
                    <li>Monitoramento de segurança</li>
                </ul>
            </div>

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">4. Compartilhamento de Dados</h2>
                <p class="text-gray-600 mb-4">
                    Não compartilhamos suas informações pessoais com terceiros, exceto:
                </p>
                <ul class="list-disc pl-6 text-gray-600 space-y-2">
                    <li>Quando exigido por lei</li>
                    <li>Para processamento de pagamentos</li>
                    <li>Com seu consentimento explícito</li>
                    <li>Para proteção de direitos legais</li>
                </ul>
            </div>

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">5. Seus Direitos</h2>
                <p class="text-gray-600 mb-4">
                    Você tem direito a:
                </p>
                <ul class="list-disc pl-6 text-gray-600 space-y-2">
                    <li>Acessar seus dados pessoais</li>
                    <li>Corrigir informações incorretas</li>
                    <li>Solicitar exclusão de dados</li>
                    <li>Revogar consentimento</li>
                    <li>Exportar seus dados</li>
                </ul>
            </div>

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">6. Cookies e Tecnologias</h2>
                <p class="text-gray-600">
                    Utilizamos cookies e tecnologias similares para melhorar sua experiência no site. Você pode controlar o uso de cookies através das configurações do seu navegador.
                </p>
            </div>

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">7. Contato</h2>
                <p class="text-gray-600">
                    Para questões sobre privacidade, entre em contato conosco através do email: contato@autorecurso.online
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