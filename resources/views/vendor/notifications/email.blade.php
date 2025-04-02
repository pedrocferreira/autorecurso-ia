@component('mail::message')
# Redefinição de Senha

Olá,

Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta.

@component('mail::button', ['url' => $actionUrl])
Redefinir Senha
@endcomponent

Este link de redefinição de senha expirará em {{ config('auth.passwords.users.expire') }} minutos.

Se você não solicitou uma redefinição de senha, nenhuma ação adicional será necessária.

Atenciosamente,<br>
{{ config('app.name') }}

<hr>

Se você estiver tendo problemas para clicar no botão "Redefinir Senha", copie e cole a URL abaixo em seu navegador web: {{ $actionUrl }}
@endcomponent 