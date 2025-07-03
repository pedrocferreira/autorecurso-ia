# 🇧🇷 Configuração da API Brasil - AutoRecurso

Esta documentação explica como configurar a integração com a **API Brasil** para consulta automática de dados de veículos por placa.

## 📋 Índice

- [O que é a API Brasil](#o-que-é-a-api-brasil)
- [Configuração](#configuração)
- [Como Obter as Credenciais](#como-obter-as-credenciais)
- [Funcionalidades](#funcionalidades)
- [Teste e Validação](#teste-e-validação)
- [Troubleshooting](#troubleshooting)

## 🇧🇷 O que é a API Brasil?

A [API Brasil](https://doc.apibrasil.io/) é um serviço brasileiro que oferece consultas a dados públicos e privados, incluindo:

- ✅ **Consulta de veículos por placa** - Dados completos do DETRAN
- ✅ **Dados oficiais** - Informações verificadas e atualizadas
- ✅ **API confiável** - Integração robusta e estável
- ✅ **Documentação completa** - Fácil implementação

## ⚙️ Configuração

### 1. Conta na API Brasil

1. Acesse [https://apibrasil.io](https://apibrasil.io)
2. Crie sua conta gratuita
3. Acesse o dashboard
4. Vá em **Configurações > API Keys**
5. Copie o **Bearer Token** (JWT)

### 2. Configuração no Sistema

Adicione a seguinte variável ao seu arquivo `.env`:

```env
# API Brasil - Consulta de Veículos
VEHICLE_API_TOKEN=seu_jwt_token_aqui
```

**Exemplo:**
```env
VEHICLE_API_TOKEN=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL3BsYXRhZm9ybWEuYXBpYnJhc2lsLmNvbS5ici9zb2NpYWwvZ2l0aHViL2NhbGxiYWNrIiwiaWF0IjoxNzUxNDc4OTM3LCJleHAiOjE3ODMwMTQ5MzcsIm5iZiI6MTc1MTQ3ODkzNywianRpIjoiVmdGSU5UZTZtdlhJaUtrUSIsInN1YiI6Ijk3MzgiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.FFHnRvxz3G2CkdxKBVuM7pbS-rQtT0Au-hoSFB-p628
```

### 3. Verificar Configuração

Execute o comando para testar a configuração:

```bash
php artisan config:clear
php artisan tinker --execute="echo env('VEHICLE_API_TOKEN') ? 'Token configurado' : 'Token não encontrado';"
```

## 🔑 Como Obter as Credenciais

### Passo a Passo Detalhado:

1. **Acesse o site**: [https://apibrasil.io](https://apibrasil.io)
2. **Crie conta** com e-mail e senha
3. **Faça login** no dashboard
4. **Vá para "Configurações"** no menu lateral
5. **Clique em "API Keys"** ou "Tokens"
6. **Copie o JWT Token** (Bearer Token)
7. **Ative um plano** com créditos para consulta de placas

### Estrutura do Token:

- **JWT Token**: Token de autenticação completo (3 partes separadas por ponto)
- **Formato**: `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.segunda_parte.terceira_parte`

## 🚀 Funcionalidades

### Consulta Automática de Veículos

Quando o usuário informa a placa no wizard, o sistema:

1. **🔍 Consulta automática** na base nacional do DETRAN
2. **📊 Retorna dados completos** como marca, modelo, ano, cor, combustível
3. **✅ Preenche automaticamente** os campos do formulário
4. **💰 Mostra custo** da consulta (aprox. R$ 0.08)
5. **🤔 Pergunta confirmação** ao usuário
6. **🔄 Permite correção** se os dados estiverem errados

### Dados Retornados:

- **Marca**: Marca do veículo (ex: "CITROEN")
- **Modelo**: Modelo completo (ex: "CITROEN/PICASSO II16GLXF")
- **Ano**: Ano de fabricação (ex: 2010)
- **Cor**: Cor do veículo (ex: "Prata")
- **Combustível**: Tipo de combustível (ex: "Alcool / Gasolina")
- **Município**: Cidade de registro (ex: "PORTO ALEGRE")
- **UF**: Estado de registro (ex: "RS")
- **Chassi**: Chassi parcial (ex: "*****VBB565699")

### Especificações Técnicas:

- **Endpoint**: `https://gateway.apibrasil.io/api/v2/vehicles/base/000/dados`
- **Método**: `POST`
- **Headers**: `Authorization: Bearer [JWT_TOKEN]`
- **Body**: `{"placa": "ABC1234", "homolog": false}`

## 🧪 Teste e Validação

### Teste Manual no Wizard:

1. Acesse `/cliente/wizard`
2. Preencha os dados pessoais
3. Informe uma placa válida (ex: "IRT6D28")
4. Veja se os dados são carregados automaticamente

### Teste via cURL:

```bash
curl -X POST https://gateway.apibrasil.io/api/v2/vehicles/base/000/dados \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer SEU_JWT_TOKEN" \
  -d '{"placa":"IRT6D28","homolog":false}'
```

### Teste via API Laravel:

```bash
curl -X POST http://seu-site.com/api/vehicle/lookup \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: seu_csrf_token" \
  -d '{"placa":"IRT6D28"}'
```

### Placas de Teste Sugeridas:

- **IRT6D28** - Placa real com dados (Citroen Picasso)
- **ABC1234** - Placa de exemplo (pode não existir)
- **ABC1D23** - Formato Mercosul

## 🔧 Troubleshooting

### Erro: "Token da API Brasil não configurado"

**Causa**: Variável VEHICLE_API_TOKEN não está definida no .env

**Solução**:
1. Verifique se a variável está no `.env`
2. Execute `php artisan config:clear`
3. Teste novamente

### Erro: "O Bearer Token informado é inválido"

**Causa**: Token expirado ou formato incorreto

**Solução**:
1. Gere um novo token no painel da API Brasil
2. Verifique se o token tem 3 partes separadas por ponto
3. Copie o token completo sem espaços ou quebras de linha

### Erro: "Plano ativo não encontrado"

**Causa**: Conta sem créditos ou plano inativo

**Solução**:
1. Acesse o painel da API Brasil
2. Vá em "Planos" ou "Créditos"
3. Ative um plano ou compre créditos
4. Aguarde alguns minutos para ativação

### Erro: "Placa não encontrada"

**Causa**: Placa não existe na base ou formato inválido

**Solução**:
1. Verifique se a placa está no formato correto (AAA0000 ou AAA0A00)
2. Teste com placas conhecidas
3. Sistema continua funcionando sem dados automáticos

### Logs de Debug:

Para verificar os logs da integração:

```bash
tail -f storage/logs/laravel.log | grep "API Brasil"
```

## 📊 Vantagens da Integração

- **⚡ Experiência superior** - Preenchimento automático instantâneo
- **📝 Dados precisos** - Informações oficiais do DETRAN
- **🚀 Processo mais rápido** - Usuário não precisa digitar manualmente
- **✅ Dados verificados** - Informações confiáveis e atualizadas
- **💰 Custo baixo** - Aproximadamente R$ 0.08 por consulta
- **🔄 Fallback inteligente** - Funciona mesmo se a API estiver indisponível

## 💡 Resposta de Exemplo

```json
{
  "success": true,
  "data": {
    "modelo": "Citroen/picasso Ii16glxf",
    "marca": "Citroen",
    "ano": 2010,
    "cor": "Prata",
    "combustivel": "Alcool / Gasolina",
    "municipio": "Porto Alegre",
    "uf": "RS",
    "chassi": "*****VBB565699",
    "placa_formatada": "IRT6328"
  },
  "api_info": {
    "balance": "2,840",
    "message": "Placa válida! Você foi tarifado em R$ 0.08!",
    "cost": "0.08"
  }
}
```

## 📚 Documentação Oficial

- **API Brasil Docs**: [https://doc.apibrasil.io/](https://doc.apibrasil.io/)
- **Endpoint Veículos**: [https://doc.apibrasil.io/apis/creditos/placa-dados-credito](https://doc.apibrasil.io/apis/creditos/placa-dados-credito)
- **Painel de Controle**: [https://apibrasil.io](https://apibrasil.io)

---

🎯 **Próximos Passos**: Após configurar o token, teste a integração criando um novo recurso no wizard e informando uma placa válida! 