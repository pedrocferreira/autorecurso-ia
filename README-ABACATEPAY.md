# 🥑 Integração AbacatePay - AutoRecurso

Esta documentação explica como configurar e usar a integração com a **AbacatePay** para pagamentos PIX no sistema AutoRecurso.

## 📋 Índice

- [O que é a AbacatePay](#o-que-é-a-abacatepay)
- [Configuração](#configuração)
- [Funcionalidades](#funcionalidades)
- [Webhooks](#webhooks)
- [Fluxo de Pagamento](#fluxo-de-pagamento)
- [Testes](#testes)
- [FAQ](#faq)

## 🥑 O que é a AbacatePay?

A [AbacatePay](https://abacatepay.com) é um gateway de pagamento brasileiro focado em simplicidade e facilidade de integração. Principais características:

- ✅ **PIX instantâneo** - Pagamentos processados em tempo real
- ✅ **API simples** - Integração descomplicada 
- ✅ **Taxas competitivas** - Custos atrativos para o mercado brasileiro
- ✅ **Webhooks confiáveis** - Notificações automáticas de pagamentos
- ✅ **Dashboard intuitivo** - Painel administrativo fácil de usar

## ⚙️ Configuração

### 1. Conta na AbacatePay

1. Acesse [https://abacatepay.com](https://abacatepay.com)
2. Crie sua conta
3. Acesse o dashboard e vá em **Configurações > API**
4. Copie sua **API Key**

### 2. Configuração no Sistema

Adicione as seguintes variáveis ao seu arquivo `.env`:

```env
# AbacatePay Configuration
ABACATEPAY_API_KEY=sua_api_key_aqui
ABACATEPAY_BASE_URL=https://api.abacatepay.com/v1
ABACATEPAY_WEBHOOK_SECRET=seu_webhook_secret_aqui
ABACATEPAY_TIMEOUT=30
ABACATEPAY_LOG_REQUESTS=true
ABACATEPAY_LOG_WEBHOOKS=true
```

### 3. Configuração do Webhook

Na AbacatePay, configure o webhook para:

- **URL**: `https://seudominio.com/abacatepay/webhook?webhookSecret=SEU_SECRET`
- **Eventos**: `billing.paid`, `billing.expired`, `billing.cancelled`

⚠️ **Importante**: O `webhookSecret` deve ser o mesmo valor configurado no `.env`

### 4. Executar Migration

```bash
php artisan migrate
```

## 🚀 Funcionalidades

### Pagamentos PIX

- **Geração automática** de QR Codes PIX
- **Verificação em tempo real** do status dos pagamentos
- **Interface responsiva** para mobile e desktop
- **Cópia fácil** do código PIX

### Sistema de Status

| Status | Descrição |
|--------|-----------|
| `pending` | Aguardando pagamento |
| `completed` | Pagamento confirmado |
| `expired` | PIX expirado (1 hora) |
| `cancelled` | Pagamento cancelado |
| `failed` | Falha no processamento |

### Histórico Detalhado

- **Método de pagamento** claramente identificado
- **Status visual** com cores e ícones
- **Referências externas** para rastreamento
- **Data de pagamento** quando aplicável

## 🔔 Webhooks

### Eventos Processados

#### `billing.paid`
- ✅ Adiciona créditos ao usuário
- ✅ Atualiza status da transação
- ✅ Registra data/hora do pagamento

#### `billing.expired`
- ⏰ Marca transação como expirada
- ⏰ Permite gerar novo PIX

#### `billing.cancelled`
- ❌ Marca transação como cancelada
- ❌ Libera para nova tentativa

### Segurança dos Webhooks

- **Validação de secret** via query parameter
- **Logs detalhados** de todos os eventos
- **Processamento idempotente** evita duplicações
- **Tratamento de erros** robusto

## 🔄 Fluxo de Pagamento

### 1. Usuário Seleciona Pacote
```
Usuário → Pacotes de Créditos → Escolhe "Pagar com PIX"
```

### 2. Geração do PIX
```
Sistema → AbacatePay API → QR Code + Código PIX gerados
```

### 3. Exibição para o Usuário
```
Interface → QR Code + Código copiável + Status em tempo real
```

### 4. Pagamento pelo Usuário
```
Usuário → App do Banco → Paga PIX
```

### 5. Confirmação Automática
```
AbacatePay → Webhook → Sistema → Créditos adicionados
```

## 🧪 Testes

### Ambiente de Teste

A AbacatePay fornece um ambiente de sandbox para testes. Configure:

```env
ABACATEPAY_BASE_URL=https://sandbox.abacatepay.com/v1
ABACATEPAY_API_KEY=sua_sandbox_api_key
```

### Testando Webhooks

Para testar webhooks em desenvolvimento local:

1. **Use ngrok ou Cloudflare Tunnel**:
   ```bash
   ngrok http 8000
   ```

2. **Configure webhook temporário**:
   ```
   https://abc123.ngrok.io/abacatepay/webhook?webhookSecret=test123
   ```

3. **Use endpoint de confirmação manual** (disponível na AbacatePay):
   ```
   POST /billing/{id}/confirm
   ```

### Comandos Úteis

```bash
# Ver logs da AbacatePay
tail -f storage/logs/laravel.log | grep -i abacate

# Limpar cache de configuração
php artisan config:clear

# Verificar rotas
php artisan route:list | grep -i abacate
```

## ❓ FAQ

### **P: Como funciona a expiração do PIX?**
**R:** O PIX expira em 1 hora. Após expirar, o usuário pode gerar um novo PIX para o mesmo pacote.

### **P: E se o webhook falhar?**
**R:** A AbacatePay possui sistema de retry automático. Além disso, implementamos verificação manual de status via API.

### **P: Os créditos são adicionados imediatamente?**
**R:** Sim! Com PIX, os créditos são adicionados em tempo real após confirmação do pagamento.

### **P: Posso usar AbacatePay junto com Stripe?**
**R:** Sim! O sistema suporta múltiplos gateways. O usuário pode escolher entre PIX (AbacatePay) e cartão (Stripe).

### **P: Como funciona a conciliação?**
**R:** Cada transação possui uma `reference` única que conecta nosso sistema com a AbacatePay para rastreamento completo.

### **P: Há limites de valor?**
**R:** A AbacatePay possui seus próprios limites. No sistema, configuramos min: R$ 1,00 e max: R$ 10.000,00 por transação.

## 🔗 Links Úteis

- [Documentação AbacatePay](https://docs.abacatepay.com)
- [Dashboard AbacatePay](https://dashboard.abacatepay.com)
- [Status da API](https://status.abacatepay.com)
- [Suporte AbacatePay](mailto:suporte@abacatepay.com)

## 🛠️ Troubleshooting

### Erro: "API Key inválida"
```bash
# Verifique se a API Key está correta no .env
echo $ABACATEPAY_API_KEY

# Limpe o cache
php artisan config:clear
```

### Webhook não recebido
```bash
# Verifique se a URL está acessível
curl -X POST https://seudominio.com/abacatepay/webhook?webhookSecret=test

# Verifique logs
tail -f storage/logs/laravel.log
```

### PIX não gerado
```bash
# Verifique conexão com API
php artisan tinker
>>> app(\App\Services\AbacatePayService::class)->getBillingStatus('test');
```

---

**Desenvolvido com ❤️ para o AutoRecurso** 