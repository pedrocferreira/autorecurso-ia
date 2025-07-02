# Instruções de Deploy - allseg.tech

## Compilação de Assets

Como não é possível executar o npm diretamente no servidor compartilhado, siga estas etapas para compilar os assets localmente:

1. Clone o repositório em seu ambiente local
2. Instale as dependências do Node.js:
   ```
   npm install
   ```
3. Compile os assets para produção:
   ```
   npm run build
   ```
4. Faça upload da pasta `public/build` gerada para o servidor

## Configuração de E-mail

Atualize a configuração de e-mail no arquivo `.env` com as credenciais corretas:

```
MAIL_MAILER=smtp
MAIL_HOST=mail.allseg.tech
MAIL_PORT=465
MAIL_USERNAME=seu-email@allseg.tech
MAIL_PASSWORD=SuaSenhaSegura
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="seu-email@allseg.tech"
MAIL_FROM_NAME="${APP_NAME}"
```

## Manutenção do Site

Para colocar o site em modo de manutenção:
```
php artisan down
```

Para reativar o site:
```
php artisan up
```

## Cache e Otimizações

Limpe e reconstrua os caches após qualquer alteração no código:
```
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Banco de Dados

Para executar migrações pendentes:
```
php artisan migrate
``` 