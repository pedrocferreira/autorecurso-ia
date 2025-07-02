<x-guest-layout>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">Termos de Serviço</h1>
                    <p class="text-gray-600">Última atualização: {{ date('d/m/Y') }}</p>
                </div>

                <div class="prose max-w-none">
                    <h2>1. DEFINIÇÕES E INTERPRETAÇÃO</h2>
                    <p>Para os fins destes Termos de Serviço, as seguintes definições se aplicam:</p>
                    <ul>
                        <li><strong>"AutoRecurso"</strong> ou <strong>"nós"</strong>: refere-se à plataforma e serviços oferecidos</li>
                        <li><strong>"Usuário"</strong> ou <strong>"você"</strong>: pessoa física que utiliza nossos serviços</li>
                        <li><strong>"Plataforma"</strong>: website e aplicações do AutoRecurso</li>
                        <li><strong>"Serviços"</strong>: geração de recursos administrativos contra multas de trânsito</li>
                        <li><strong>"Recurso"</strong>: documento legal gerado pela nossa IA para contestação de multas</li>
                        <li><strong>"Créditos"</strong>: unidade de medida para utilização dos serviços</li>
                    </ul>

                    <h2>2. ACEITAÇÃO DOS TERMOS</h2>
                    <p>Ao acessar e utilizar a plataforma AutoRecurso, você concorda integralmente com estes Termos de Serviço e nossa Política de Privacidade. Se você não concorda com qualquer parte destes termos, não deve utilizar nossos serviços.</p>
                    
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 my-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-yellow-700">
                                    <strong>Importante:</strong> Estes termos constituem um acordo legalmente vinculativo entre você e o AutoRecurso.
                                </p>
                            </div>
                        </div>
                    </div>

                    <h2>3. DESCRIÇÃO DOS SERVIÇOS</h2>
                    
                    <h3>3.1. Serviços Oferecidos</h3>
                    <p>O AutoRecurso oferece:</p>
                    <ul>
                        <li>Geração automatizada de recursos administrativos contra multas de trânsito</li>
                        <li>Utilização de múltiplas tecnologias de Inteligência Artificial (GPT-4, Gemini, RoBERTa)</li>
                        <li>Personalização de recursos com base nos dados fornecidos pelo usuário</li>
                        <li>Geração de documentos em formato PDF prontos para protocolo</li>
                        <li>Sistema de créditos para utilização dos serviços</li>
                    </ul>

                    <h3>3.2. Limitações dos Serviços</h3>
                    <p>Nossos serviços são limitados a:</p>
                    <ul>
                        <li>Geração de recursos administrativos de primeira instância</li>
                        <li>Multas de trânsito comuns (velocidade, estacionamento, semáforo, etc.)</li>
                        <li>Documentos baseados na legislação brasileira de trânsito</li>
                    </ul>

                    <h2>4. CADASTRO E CONTA DO USUÁRIO</h2>
                    
                    <h3>4.1. Requisitos para Cadastro</h3>
                    <p>Para utilizar nossos serviços, você deve:</p>
                    <ul>
                        <li>Ser maior de 18 anos</li>
                        <li>Fornecer informações verdadeiras, precisas e completas</li>
                        <li>Manter suas informações atualizadas</li>
                        <li>Ser o proprietário ou condutor autorizado do veículo</li>
                    </ul>

                    <h3>4.2. Responsabilidades do Usuário</h3>
                    <p>Você é responsável por:</p>
                    <ul>
                        <li>Manter a confidencialidade de sua senha</li>
                        <li>Todas as atividades realizadas em sua conta</li>
                        <li>Notificar imediatamente sobre uso não autorizado</li>
                        <li>Fornecer dados precisos sobre a infração</li>
                    </ul>

                    <h2>5. SISTEMA DE CRÉDITOS E PAGAMENTOS</h2>
                    
                    <h3>5.1. Funcionamento dos Créditos</h3>
                    <ul>
                        <li>Cada recurso gerado consome 3 créditos (Inteligência Híbrida)</li>
                        <li>Créditos são adquiridos através de pacotes pré-pagos</li>
                        <li>Créditos não utilizados não expiram</li>
                        <li>Créditos não são reembolsáveis após a compra</li>
                    </ul>

                    <h3>5.2. Preços e Pagamentos</h3>
                    <p>Os preços atuais são:</p>
                    <ul>
                        <li><strong>5 Créditos:</strong> R$ 29,90 (1-2 recursos)</li>
                        <li><strong>10 Créditos:</strong> R$ 49,90 (3-4 recursos)</li>
                        <li><strong>20 Créditos:</strong> R$ 89,90 (6-7 recursos)</li>
                        <li><strong>50 Créditos:</strong> R$ 199,90 (16-17 recursos)</li>
                    </ul>

                    <h3>5.3. Processamento de Pagamentos</h3>
                    <ul>
                        <li>Pagamentos processados via Stripe (cartão de crédito)</li>
                        <li>Todas as transações são seguras e criptografadas</li>
                        <li>Cobrança em Reais (BRL)</li>
                        <li>Recibo enviado por e-mail após confirmação</li>
                    </ul>

                    <h2>6. USO ACEITÁVEL E PROIBIÇÕES</h2>
                    
                    <h3>6.1. Uso Permitido</h3>
                    <p>Você pode usar nossos serviços para:</p>
                    <ul>
                        <li>Gerar recursos para suas próprias multas de trânsito</li>
                        <li>Auxiliar familiares com recursos (com autorização)</li>
                        <li>Fins educacionais sobre legislação de trânsito</li>
                    </ul>

                    <h3>6.2. Uso Proibido</h3>
                    <p>É expressamente proibido:</p>
                    <ul>
                        <li>Fornecer informações falsas ou fraudulentas</li>
                        <li>Utilizar para fins comerciais sem autorização</li>
                        <li>Revender ou redistribuir nossos serviços</li>
                        <li>Tentar contornar limitações técnicas</li>
                        <li>Usar para spam ou atividades maliciosas</li>
                        <li>Violar direitos de propriedade intelectual</li>
                    </ul>

                    <h2>7. PROPRIEDADE INTELECTUAL</h2>
                    
                    <h3>7.1. Direitos do AutoRecurso</h3>
                    <p>Todos os direitos de propriedade intelectual sobre a plataforma, incluindo mas não limitado a:</p>
                    <ul>
                        <li>Software e código-fonte</li>
                        <li>Design e interface</li>
                        <li>Algoritmos de IA</li>
                        <li>Modelos de documentos</li>
                        <li>Marca e logotipos</li>
                    </ul>

                    <h3>7.2. Licença de Uso</h3>
                    <p>Concedemos a você uma licença limitada, não exclusiva e revogável para usar nossos serviços conforme estes termos.</p>

                    <h3>7.3. Conteúdo Gerado</h3>
                    <p>Os recursos gerados são de sua propriedade, mas você nos concede direito de:</p>
                    <ul>
                        <li>Armazenar para fins de backup e suporte</li>
                        <li>Usar para melhoria dos serviços (dados anonimizados)</li>
                        <li>Cumprir obrigações legais quando necessário</li>
                    </ul>

                    <h2>8. PRIVACIDADE E PROTEÇÃO DE DADOS</h2>
                    <p>O tratamento de seus dados pessoais é regido por nossa <a href="{{ route('legal.privacy') }}" class="text-blue-600 hover:underline">Política de Privacidade</a>, que faz parte integrante destes Termos de Serviço.</p>

                    <h2>9. LIMITAÇÕES DE RESPONSABILIDADE</h2>
                    
                    <div class="bg-red-50 border-l-4 border-red-400 p-6 my-6">
                        <h3 class="text-lg font-semibold text-red-800 mb-4">Importante - Limitações Legais</h3>
                        <div class="text-red-700 space-y-3">
                            <p><strong>9.1. Natureza do Serviço:</strong> O AutoRecurso é uma ferramenta de auxílio na geração de recursos administrativos. Não garantimos o sucesso dos recursos gerados.</p>
                            
                            <p><strong>9.2. Não Somos Advogados:</strong> Nossos serviços não constituem consultoria jurídica. Para casos complexos, recomendamos consultar um advogado especializado.</p>
                            
                            <p><strong>9.3. Responsabilidade do Usuário:</strong> Você é inteiramente responsável por:</p>
                            <ul class="ml-6 mt-2">
                                <li>Verificar a precisão dos dados fornecidos</li>
                                <li>Revisar o recurso gerado antes do protocolo</li>
                                <li>Cumprir prazos legais para apresentação</li>
                                <li>Consequências do uso dos recursos gerados</li>
                            </ul>
                        </div>
                    </div>

                    <h3>9.4. Exclusão de Garantias</h3>
                    <p>Os serviços são fornecidos "como estão", sem garantias de qualquer tipo, incluindo:</p>
                    <ul>
                        <li>Adequação a propósitos específicos</li>
                        <li>Resultados específicos ou sucesso dos recursos</li>
                        <li>Funcionamento ininterrupto dos serviços</li>
                        <li>Ausência de erros ou falhas</li>
                    </ul>

                    <h3>9.5. Limitação de Danos</h3>
                    <p>Em nenhuma hipótese seremos responsáveis por danos indiretos, consequenciais, especiais ou punitivos, incluindo lucros cessantes, mesmo que tenhamos sido informados da possibilidade de tais danos.</p>

                    <h2>10. DISPONIBILIDADE E MANUTENÇÃO</h2>
                    <ul>
                        <li>Nos esforçamos para manter 99% de uptime</li>
                        <li>Manutenções programadas serão comunicadas com antecedência</li>
                        <li>Não nos responsabilizamos por interrupções causadas por terceiros</li>
                        <li>Backups são realizados diariamente para proteção de dados</li>
                    </ul>

                    <h2>11. MODIFICAÇÕES DOS TERMOS</h2>
                    <p>Podemos modificar estes Termos de Serviço a qualquer momento. Mudanças significativas serão comunicadas através de:</p>
                    <ul>
                        <li>E-mail para usuários registrados</li>
                        <li>Aviso destacado na plataforma</li>
                        <li>Atualização da data de modificação</li>
                    </ul>
                    <p>O uso continuado após as modificações constitui aceitação dos novos termos.</p>

                    <h2>12. RESCISÃO</h2>
                    
                    <h3>12.1. Rescisão pelo Usuário</h3>
                    <p>Você pode encerrar sua conta a qualquer momento através das configurações da conta ou entrando em contato conosco.</p>

                    <h3>12.2. Rescisão pelo AutoRecurso</h3>
                    <p>Podemos suspender ou encerrar sua conta imediatamente em caso de:</p>
                    <ul>
                        <li>Violação destes Termos de Serviço</li>
                        <li>Atividade fraudulenta ou suspeita</li>
                        <li>Não pagamento de valores devidos</li>
                        <li>Uso indevido da plataforma</li>
                    </ul>

                    <h3>12.3. Efeitos da Rescisão</h3>
                    <p>Após o encerramento:</p>
                    <ul>
                        <li>Acesso à conta será removido</li>
                        <li>Créditos não utilizados serão perdidos</li>
                        <li>Dados podem ser mantidos conforme Política de Privacidade</li>
                        <li>Obrigações financeiras permanecem válidas</li>
                    </ul>

                    <h2>13. POLÍTICA DE REEMBOLSO</h2>
                    
                    <h3>13.1. Créditos</h3>
                    <ul>
                        <li>Créditos não são reembolsáveis após a compra</li>
                        <li>Exceção: falhas técnicas que impeçam o uso</li>
                        <li>Reembolsos processados em até 30 dias úteis</li>
                    </ul>

                    <h3>13.2. Recursos Gerados</h3>
                    <ul>
                        <li>Não oferecemos reembolso por recursos já gerados</li>
                        <li>Exceção: falhas técnicas na geração</li>
                        <li>Problemas devem ser reportados em até 48 horas</li>
                    </ul>

                    <h2>14. SUPORTE AO CLIENTE</h2>
                    <p>Oferecemos suporte através de:</p>
                    <ul>
                        <li><strong>E-mail:</strong> suporte@autorecurso.online</li>
                        <li><strong>Horário:</strong> Segunda a sexta, 9h às 18h</li>
                        <li><strong>Tempo de resposta:</strong> Até 24 horas úteis</li>
                        <li><strong>Idioma:</strong> Português brasileiro</li>
                    </ul>

                    <h2>15. LEGISLAÇÃO APLICÁVEL E FORO</h2>
                    <p>Estes Termos de Serviço são regidos pelas leis brasileiras. Qualquer disputa será resolvida no foro da comarca de São Paulo/SP, com renúncia expressa a qualquer outro, por mais privilegiado que seja.</p>

                    <h2>16. DISPOSIÇÕES GERAIS</h2>
                    
                    <h3>16.1. Integralidade</h3>
                    <p>Estes Termos, juntamente com a Política de Privacidade, constituem o acordo integral between você e o AutoRecurso.</p>

                    <h3>16.2. Independência das Cláusulas</h3>
                    <p>Se qualquer disposição for considerada inválida, as demais permanecerão em pleno vigor.</p>

                    <h3>16.3. Cessão</h3>
                    <p>Você não pode ceder seus direitos sem nosso consentimento prévio. Podemos ceder nossos direitos a qualquer momento.</p>

                    <h3>16.4. Força Maior</h3>
                    <p>Não seremos responsáveis por atrasos ou falhas causados por circunstâncias além de nosso controle razoável.</p>

                    <h2>17. CONTATO</h2>
                    <div class="bg-gray-50 p-6 rounded-lg my-6">
                        <h3 class="font-semibold mb-4">Informações de Contato</h3>
                        <p><strong>AutoRecurso</strong><br>
                        E-mail: contato@autorecurso.online<br>
                        Suporte: suporte@autorecurso.online<br>
                        Website: {{ config('app.url') }}</p>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-400 p-6 my-8">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-blue-800">Compromisso com a Transparência</h3>
                                <p class="mt-2 text-blue-700">
                                    O AutoRecurso está comprometido em fornecer serviços transparentes e éticos. 
                                    Estes termos foram elaborados para proteger tanto seus direitos quanto os nossos, 
                                    garantindo uma relação comercial justa e transparente.
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-8">
                    
                    <p class="text-sm text-gray-600">
                        <strong>Documento válido a partir de:</strong> {{ date('d/m/Y') }}<br>
                        <strong>Versão:</strong> 1.0<br>
                        <strong>Próxima revisão:</strong> {{ date('d/m/Y', strtotime('+6 months')) }}
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