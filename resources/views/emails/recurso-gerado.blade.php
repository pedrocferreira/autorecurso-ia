<x-mail::message>
# Olá, {{ $user->name }}!

Seu recurso de multa de trânsito foi gerado com sucesso em nossa plataforma.

Estamos muito felizes em poder te ajudar a contestar sua multa de forma rápida e inteligente. Abaixo estão os detalhes e o link para você acessar seu documento.

**Detalhes da Multa:**
- **Placa do Veículo:** {{ $appeal->ticket->plate }}
- **Auto de Infração:** {{ $appeal->ticket->infraction_number }}

Para visualizar e baixar seu recurso em formato PDF, clique no botão abaixo:

<x-mail::button :url="route('appeals.show', $appeal)">
Ver Meu Recurso
</x-mail::button>

Lembre-se de imprimir o documento, assinar e entregar no órgão de trânsito responsável.

Se tiver qualquer dúvida, basta responder a este e-mail.

Atenciosamente,<br>
Equipe {{ config('app.name') }}
</x-mail::message>
