# 🔒 Checklist de Segurança - AutoRecurso

## 🔑 Chaves de API e Credenciais

- [ ] Remover todas as chaves de API de arquivos de documentação
- [ ] Verificar se o `.env.example` não contém chaves reais
- [ ] Rotacionar todas as chaves de API antes do deploy
- [ ] Configurar variáveis de ambiente no servidor de produção
- [ ] Verificar permissões do arquivo `.env` no servidor (600)

## 🌐 Configurações de CORS

- [ ] Verificar se `allowed_origins` contém apenas domínios necessários
- [ ] Confirmar se `APP_URL` e `FRONTEND_URL` estão corretos no `.env`
- [ ] Validar lista de `allowed_headers`
- [ ] Testar CORS com domínios de produção

## 🔐 Segurança de Sessão

- [ ] Configurar `SESSION_SECURE_COOKIE=true` em produção
- [ ] Verificar `SESSION_DOMAIN` para cookies
- [ ] Confirmar `same_site=lax` para cookies
- [ ] Validar tempo de expiração de sessão

## 💳 Integrações de Pagamento

- [ ] Verificar webhook secrets do AbacatePay
- [ ] Implementar validação HMAC para webhooks
- [ ] Confirmar URLs de retorno de pagamento
- [ ] Testar fluxo completo de pagamento em ambiente de staging

## 📝 Logs e Monitoramento

- [ ] Revisar todos os `Log::info()` para remover dados sensíveis
- [ ] Configurar log rotation
- [ ] Implementar monitoramento de erros (ex: Sentry)
- [ ] Configurar alertas para tentativas de acesso suspeitas

## 🔒 Segurança Geral

- [ ] Atualizar todas as dependências para últimas versões
- [ ] Executar scan de vulnerabilidades
- [ ] Configurar headers de segurança (X-Frame-Options, etc)
- [ ] Implementar rate limiting em rotas sensíveis
- [ ] Verificar políticas de senha
- [ ] Configurar backup automático
- [ ] Implementar 2FA para contas admin

## 🚀 Deploy

- [ ] Configurar HTTPS
- [ ] Verificar certificados SSL
- [ ] Testar redirecionamento HTTP -> HTTPS
- [ ] Configurar firewall
- [ ] Revisar permissões de arquivos/diretórios
- [ ] Desativar debug mode
- [ ] Limpar cache de rotas e configurações

## 📋 Documentação

- [ ] Atualizar documentação de deploy
- [ ] Documentar procedimentos de recuperação
- [ ] Criar guia de troubleshooting
- [ ] Documentar contatos de emergência

## ✅ Pós-Deploy

- [ ] Executar testes de penetração básicos
- [ ] Verificar logs de erro
- [ ] Monitorar métricas de performance
- [ ] Validar backup/restore
- [ ] Testar procedimentos de recuperação 