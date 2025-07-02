<x-guest-layout>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">Política de Privacidade</h1>
                    <p class="text-gray-600">Última atualização: {{ date('d/m/Y') }}</p>
                </div>

                <div class="prose max-w-none">
                    <h2>1. INFORMAÇÕES GERAIS</h2>
                    <p>Esta Política de Privacidade descreve como o <strong>AutoRecurso</strong> ("nós", "nosso" ou "empresa") coleta, usa, armazena e protege suas informações pessoais quando você utiliza nossa plataforma online de geração de recursos administrativos contra multas de trânsito.</p>
                    
                    <p><strong>Controlador de Dados:</strong> AutoRecurso<br>
                    <strong>Website:</strong> {{ config('app.url') }}<br>
                    <strong>E-mail para contato:</strong> contato@autorecurso.online</p>

                    <h2>2. BASE LEGAL PARA PROCESSAMENTO</h2>
                    <p>Processamos seus dados pessoais com base nas seguintes bases legais previstas na Lei Geral de Proteção de Dados (LGPD - Lei 13.709/2018):</p>
                    <ul>
                        <li><strong>Consentimento:</strong> Para dados fornecidos voluntariamente</li>
                        <li><strong>Execução de contrato:</strong> Para prestação dos serviços contratados</li>
                        <li><strong>Interesse legítimo:</strong> Para melhoria dos serviços e segurança</li>
                        <li><strong>Cumprimento de obrigação legal:</strong> Para atendimento de requisitos legais</li>
                    </ul>

                    <h2>3. DADOS COLETADOS</h2>
                    
                    <h3>3.1. Dados fornecidos diretamente por você:</h3>
                    <ul>
                        <li><strong>Dados de identificação:</strong> Nome completo, CPF, CNH, endereço, telefone, e-mail</li>
                        <li><strong>Dados do veículo:</strong> Placa, modelo, ano, cor, chassi, RENAVAM</li>
                        <li><strong>Dados da infração:</strong> Data, local, valor, motivo da autuação, número da multa</li>
                        <li><strong>Dados de pagamento:</strong> Informações de cartão de crédito (processadas pela Stripe)</li>
                        <li><strong>Dados de autenticação:</strong> Senha criptografada, dados do Google OAuth (quando aplicável)</li>
                    </ul>

                    <h3>3.2. Dados coletados automaticamente:</h3>
                    <ul>
                        <li><strong>Dados de navegação:</strong> Endereço IP, tipo de navegador, páginas visitadas, tempo de acesso</li>
                        <li><strong>Cookies e tecnologias similares:</strong> Para funcionalidade e análise do site</li>
                        <li><strong>Logs de sistema:</strong> Para segurança e funcionamento da plataforma</li>
                    </ul>

                    <h2>4. FINALIDADES DO TRATAMENTO</h2>
                    <p>Utilizamos seus dados pessoais para:</p>
                    <ul>
                        <li>Gerar recursos administrativos personalizados contra multas de trânsito</li>
                        <li>Processar pagamentos e gerenciar sua conta</li>
                        <li>Enviar comunicações sobre o serviço e atualizações</li>
                        <li>Melhorar nossos serviços através de análises e estatísticas</li>
                        <li>Garantir a segurança da plataforma</li>
                        <li>Cumprir obrigações legais e regulamentares</li>
                        <li>Exercer direitos em processos judiciais, administrativos ou arbitrais</li>
                    </ul>

                    <h2>5. COMPARTILHAMENTO DE DADOS</h2>
                    <p>Compartilhamos seus dados apenas nas seguintes situações:</p>
                    
                    <h3>5.1. Prestadores de serviços:</h3>
                    <ul>
                        <li><strong>Stripe:</strong> Para processamento de pagamentos</li>
                        <li><strong>Google:</strong> Para autenticação OAuth e serviços de IA</li>
                        <li><strong>OpenAI:</strong> Para geração de conteúdo (dados anonimizados)</li>
                        <li><strong>Hugging Face:</strong> Para processamento de linguagem natural</li>
                        <li><strong>Provedores de hospedagem:</strong> Para armazenamento seguro dos dados</li>
                    </ul>

                    <h3>5.2. Autoridades competentes:</h3>
                    <p>Quando exigido por lei, ordem judicial ou para proteção de direitos.</p>

                    <h2>6. TRANSFERÊNCIA INTERNACIONAL</h2>
                    <p>Alguns de nossos prestadores de serviços estão localizados fora do Brasil. Garantimos que:</p>
                    <ul>
                        <li>Todos os prestadores atendem a padrões adequados de proteção de dados</li>
                        <li>Implementamos salvaguardas contratuais apropriadas</li>
                        <li>Seguimos as diretrizes da ANPD para transferências internacionais</li>
                    </ul>

                    <h2>7. SEGURANÇA DOS DADOS</h2>
                    <p>Implementamos medidas técnicas e organizacionais para proteger seus dados:</p>
                    <ul>
                        <li>Criptografia SSL/TLS para transmissão de dados</li>
                        <li>Criptografia de dados sensíveis em repouso</li>
                        <li>Controles de acesso rigorosos</li>
                        <li>Monitoramento de segurança 24/7</li>
                        <li>Backups regulares e seguros</li>
                        <li>Auditorias de segurança periódicas</li>
                    </ul>

                    <h2>8. RETENÇÃO DE DADOS</h2>
                    <p>Mantemos seus dados pelo período necessário para:</p>
                    <ul>
                        <li><strong>Dados da conta:</strong> Enquanto a conta estiver ativa + 5 anos após encerramento</li>
                        <li><strong>Dados de recursos:</strong> 10 anos (conforme legislação de trânsito)</li>
                        <li><strong>Dados de pagamento:</strong> 5 anos (conforme legislação fiscal)</li>
                        <li><strong>Logs de segurança:</strong> 6 meses</li>
                    </ul>

                    <h2>9. SEUS DIREITOS (LGPD)</h2>
                    <p>Você tem os seguintes direitos sobre seus dados pessoais:</p>
                    
                    <div class="bg-blue-50 p-6 rounded-lg my-6">
                        <h3 class="text-lg font-semibold text-blue-800 mb-4">Direitos do Titular dos Dados</h3>
                        <ul class="space-y-2">
                            <li><strong>Confirmação e acesso:</strong> Saber se processamos seus dados e acessá-los</li>
                            <li><strong>Correção:</strong> Corrigir dados incompletos, inexatos ou desatualizados</li>
                            <li><strong>Anonimização ou eliminação:</strong> Quando desnecessários, excessivos ou tratados em desconformidade</li>
                            <li><strong>Portabilidade:</strong> Receber seus dados em formato estruturado</li>
                            <li><strong>Eliminação:</strong> Excluir dados tratados com base no consentimento</li>
                            <li><strong>Informação:</strong> Sobre compartilhamento de dados com terceiros</li>
                            <li><strong>Revogação do consentimento:</strong> Retirar consentimento a qualquer momento</li>
                            <li><strong>Revisão:</strong> Solicitar revisão de decisões automatizadas</li>
                        </ul>
                    </div>

                    <h3>Como exercer seus direitos:</h3>
                    <p>Para exercer qualquer um desses direitos, entre em contato conosco através de:</p>
                    <ul>
                        <li><strong>E-mail:</strong> privacidade@autorecurso.online</li>
                        <li><strong>Formulário online:</strong> <a href="{{ route('privacy.request') }}" class="text-blue-600 hover:underline">Solicitação de Dados</a></li>
                    </ul>
                    <p>Responderemos sua solicitação em até 15 dias úteis.</p>

                    <h2>10. COOKIES</h2>
                    <p>Utilizamos cookies e tecnologias similares para:</p>
                    <ul>
                        <li><strong>Cookies essenciais:</strong> Funcionamento básico do site</li>
                        <li><strong>Cookies de funcionalidade:</strong> Lembrar suas preferências</li>
                        <li><strong>Cookies analíticos:</strong> Entender como você usa o site</li>
                        <li><strong>Cookies de marketing:</strong> Personalizar anúncios (com seu consentimento)</li>
                    </ul>
                    
                    <p>Você pode gerenciar cookies através das configurações do seu navegador ou através do nosso <a href="#" class="text-blue-600 hover:underline">Centro de Preferências de Cookies</a>.</p>

                    <h2>11. MENORES DE IDADE</h2>
                    <p>Nossos serviços são destinados a pessoas maiores de 18 anos. Não coletamos intencionalmente dados de menores de idade. Se tomarmos conhecimento de que coletamos dados de um menor, excluiremos essas informações imediatamente.</p>

                    <h2>12. ALTERAÇÕES NESTA POLÍTICA</h2>
                    <p>Podemos atualizar esta Política de Privacidade periodicamente. Notificaremos sobre mudanças significativas através de:</p>
                    <ul>
                        <li>E-mail para usuários registrados</li>
                        <li>Aviso em destaque no site</li>
                        <li>Atualização da data de "última modificação"</li>
                    </ul>

                    <h2>13. CONTATO E ENCARREGADO DE DADOS</h2>
                    <p>Para questões sobre esta Política de Privacidade ou tratamento de dados pessoais:</p>
                    
                    <div class="bg-gray-50 p-6 rounded-lg my-6">
                        <h3 class="font-semibold mb-4">Dados para Contato</h3>
                        <p><strong>AutoRecurso - Controlador de Dados</strong><br>
                        E-mail: contato@autorecurso.online<br>
                        E-mail do Encarregado (DPO): dpo@autorecurso.online</p>
                    </div>

                    <h2>14. AUTORIDADE DE PROTEÇÃO DE DADOS</h2>
                    <p>Você também pode entrar em contato com a Autoridade Nacional de Proteção de Dados (ANPD):</p>
                    <ul>
                        <li><strong>Website:</strong> <a href="https://www.gov.br/anpd/" target="_blank" class="text-blue-600 hover:underline">www.gov.br/anpd</a></li>
                        <li><strong>E-mail:</strong> atendimento@anpd.gov.br</li>
                    </ul>

                    <h2>15. CONSENTIMENTO</h2>
                    <p>Ao utilizar nossos serviços, você declara ter lido, compreendido e concordado com esta Política de Privacidade.</p>

                    <div class="bg-green-50 border-l-4 border-green-400 p-6 my-8">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-shield-alt text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-green-800">Compromisso com sua Privacidade</h3>
                                <p class="mt-2 text-green-700">
                                    O AutoRecurso está comprometido com a proteção de seus dados pessoais e o cumprimento integral da LGPD. 
                                    Implementamos as melhores práticas de segurança e transparência no tratamento de suas informações.
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-8">
                    
                    <p class="text-sm text-gray-600">
                        <strong>Documento válido a partir de:</strong> {{ date('d/m/Y') }}<br>
                        <strong>Versão:</strong> 1.0<br>
                        <strong>Próxima revisão:</strong> {{ date('d/m/Y', strtotime('+1 year')) }}
                    </p>
                </div>

                <div class="mt-8 text-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

@push('styles')
<style>
    .prose h2 {
        @apply text-xl font-bold text-gray-900 mt-8 mb-4;
    }
    .prose h3 {
        @apply text-lg font-semibold text-gray-800 mt-6 mb-3;
    }
    .prose p {
        @apply text-gray-700 mb-4 leading-relaxed;
    }
    .prose ul {
        @apply mb-4 pl-6;
    }
    .prose li {
        @apply mb-2 text-gray-700;
    }
    .prose strong {
        @apply font-semibold text-gray-900;
    }
    .prose a {
        @apply text-blue-600 hover:text-blue-800 hover:underline;
    }
</style>
@endpush 