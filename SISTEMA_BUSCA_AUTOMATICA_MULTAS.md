# 🚗 Sistema de Busca Automática de Multas de Trânsito

## 📋 Visão Geral

Este sistema permite buscar automaticamente multas de trânsito usando APIs oficiais (DETRAN, DENATRAN, SPP) e integrar com o banco de dados local para gerenciamento completo.

## ✨ Funcionalidades

### 🔍 Busca Automática
- **Por CPF**: Busca multas associadas ao CPF do usuário
- **Por CNH**: Busca multas associadas à CNH do usuário  
- **Por Placa**: Busca multas associadas a veículos específicos
- **Busca Completa**: Combina todas as fontes em uma única operação

### 🔗 Integração com APIs
- **DETRAN Estadual**: Multas de trânsito estaduais
- **DENATRAN Federal**: Multas de trânsito federais
- **SPP**: Sistema de Pontuação da CNH

### 💾 Sincronização Automática
- Sincronização em background via comando Artisan
- Cache inteligente para evitar consultas desnecessárias
- Sincronização automática no login (configurável)

## 🏗️ Arquitetura

### Serviços
- `TrafficTicketService`: Lógica principal de busca e sincronização
- `TrafficTicketController`: Controller para endpoints da API
- Comando Artisan para sincronização automática

### Rotas
```
GET    /traffic-tickets                    # Página principal
POST   /traffic-tickets/search-cpf         # Busca por CPF
POST   /traffic-tickets/search-cnh         # Busca por CNH
POST   /traffic-tickets/search-plate       # Busca por placa
POST   /traffic-tickets/search-all         # Busca completa
GET    /traffic-tickets/statistics         # Estatísticas
```

## 🚀 Como Usar

### 1. Acessar a Página
- Navegue para `/traffic-tickets` no menu lateral
- Ou clique em "Buscar Multas" na navegação

### 2. Buscar Multas
- **CPF**: Digite seu CPF no formato 000.000.000-00
- **CNH**: Digite o número da sua CNH
- **Placa**: Digite a placa do veículo (formato AAA-0000)
- **Busca Completa**: Clique em "Buscar Tudo" para todas as fontes

### 3. Visualizar Resultados
- Multas encontradas são exibidas com detalhes completos
- Cada multa mostra: data, placa, motivo, valor, pontos, local
- Botão "Gerar Recurso" para cada multa encontrada

### 4. Sincronização Automática
- O sistema sincroniza automaticamente no login
- Comando manual: `php artisan traffic-tickets:sync`
- Sincronização completa: `php artisan traffic-tickets:sync --all`

## ⚙️ Configuração

### Variáveis de Ambiente
```env
# Habilitar sistema
TRAFFIC_TICKETS_ENABLED=true

# DETRAN
DETRAN_ENABLED=true
DETRAN_BASE_URL=https://api.detran.gov.br
DETRAN_API_KEY=sua_chave_api

# DENATRAN  
DENATRAN_ENABLED=true
DENATRAN_BASE_URL=https://api.denatran.gov.br
DENATRAN_API_KEY=sua_chave_api

# SPP
SPP_ENABLED=true
SPP_BASE_URL=https://api.spp.gov.br
SPP_API_KEY=sua_chave_api

# Cache
TRAFFIC_TICKETS_CACHE_ENABLED=true
TRAFFIC_TICKETS_CACHE_TTL=3600

# Simulação (desenvolvimento)
TRAFFIC_TICKETS_SIMULATION_ENABLED=true
```

### Configuração do Cache
```php
// config/cache.php
'stores' => [
    'traffic_tickets' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'prefix' => 'traffic_tickets:',
    ],
],
```

## 🔧 Desenvolvimento

### Estrutura de Arquivos
```
app/
├── Services/
│   └── TrafficTicketService.php          # Lógica de negócio
├── Http/Controllers/
│   └── TrafficTicketController.php       # Controller da API
├── Console/Commands/
│   └── SyncTrafficTickets.php            # Comando de sincronização
└── Models/
    ├── User.php                           # Usuário (já existe)
    └── Ticket.php                         # Multa (já existe)

resources/views/
└── traffic-tickets/
    └── index.blade.php                    # Interface principal

config/
└── traffic-tickets.php                    # Configurações

routes/
└── authenticated.php                      # Rotas autenticadas
```

### Adicionando Nova API
1. Criar método no `TrafficTicketService`
2. Adicionar configurações no `config/traffic-tickets.php`
3. Integrar no método `searchTicketsByDocument`
4. Adicionar variáveis de ambiente

### Exemplo de Nova API
```php
private function searchNewApiTickets(string $document, User $user): array
{
    try {
        $apiKey = config('traffic-tickets.new_api.api_key');
        $baseUrl = config('traffic-tickets.new_api.base_url');
        
        // Fazer requisição para a API
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Accept' => 'application/json'
        ])->get("{$baseUrl}/tickets", [
            'document' => $document
        ]);
        
        if ($response->successful()) {
            return [
                'success' => true,
                'tickets' => $this->formatApiResponse($response->json()),
                'source' => 'NEW_API'
            ];
        }
        
        return ['success' => false, 'tickets' => []];
        
    } catch (\Exception $e) {
        Log::error('Erro na busca NEW_API: ' . $e->getMessage());
        return ['success' => false, 'tickets' => []];
    }
}
```

## 📊 Monitoramento

### Logs
- Todas as operações são logadas com detalhes
- Nível configurável via `TRAFFIC_TICKETS_LOG_LEVEL`
- Dados sensíveis são mascarados automaticamente

### Métricas
- Total de multas encontradas por fonte
- Tempo de resposta das APIs
- Taxa de sucesso das consultas
- Uso de cache

### Comandos de Monitoramento
```bash
# Ver estatísticas
php artisan traffic-tickets:stats

# Verificar status das APIs
php artisan traffic-tickets:health

# Limpar cache
php artisan traffic-tickets:clear-cache
```

## 🚨 Tratamento de Erros

### Falhas de API
- Retry automático configurável
- Fallback para dados locais
- Notificação de falhas para administradores

### Rate Limiting
- Limite de requisições por minuto/hora
- Fila de processamento para grandes volumes
- Cache inteligente para reduzir consultas

### Validação de Dados
- Validação de formato de documentos
- Sanitização de dados recebidos
- Verificação de integridade

## 🔒 Segurança

### Proteção de Dados
- Dados sensíveis são mascarados nos logs
- Autenticação obrigatória para todas as operações
- Rate limiting para prevenir abuso

### Auditoria
- Log de todas as consultas realizadas
- Rastreamento de usuários que acessaram dados
- Histórico de sincronizações

## 📈 Melhorias Futuras

### Funcionalidades Planejadas
- [ ] Notificações push para novas multas
- [ ] Dashboard de estatísticas avançadas
- [ ] Integração com mais órgãos estaduais
- [ ] Sistema de alertas para vencimento
- [ ] Relatórios personalizados

### Otimizações Técnicas
- [ ] Queue para processamento assíncrono
- [ ] Cache distribuído (Redis cluster)
- [ ] API GraphQL para consultas complexas
- [ ] Webhooks para atualizações em tempo real

## 🤝 Contribuição

### Padrões de Código
- PSR-12 para estilo de código
- Testes unitários para todos os métodos
- Documentação inline para métodos complexos
- Logs estruturados para monitoramento

### Processo de Desenvolvimento
1. Criar branch para nova funcionalidade
2. Implementar com testes
3. Documentar mudanças
4. Criar Pull Request
5. Code review obrigatório

## 📞 Suporte

### Problemas Comuns
- **API não responde**: Verificar configurações e conectividade
- **Cache não funciona**: Verificar configurações do Redis
- **Sincronização falha**: Verificar logs e permissões

### Contatos
- Desenvolvedor: [Seu Nome]
- Email: [seu.email@exemplo.com]
- Documentação: [Link para docs]

---

**Versão**: 1.0.0  
**Última Atualização**: {{ date('d/m/Y') }}  
**Status**: ✅ Implementado e Funcionando
