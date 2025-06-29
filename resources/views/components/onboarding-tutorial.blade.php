<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js/minified/introjs.min.css">
<script src="https://cdn.jsdelivr.net/npm/intro.js/minified/intro.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof introJs === 'undefined') {
            return;
        }

        const intro = introJs();
        intro.setOptions({
            nextLabel: 'Próximo',
            prevLabel: 'Voltar',
            doneLabel: 'Concluir',
            overlayOpacity: 0.6,
            steps: [
                {
                    title: 'Bem-vindo ao AutoRecurso!',
                    intro: 'Vamos apresentar rapidamente as principais áreas do sistema. 😊'
                },
                {
                    element: document.querySelector('#btn-generate-appeal'),
                    intro: 'Clique aqui para gerar um novo recurso usando nossa Inteligência Híbrida.',
                    position: 'bottom'
                },
                {
                    element: document.querySelector('#btn-new-ticket'),
                    intro: 'Aqui você cadastra uma nova multa manualmente.',
                    position: 'bottom'
                },
                {
                    element: document.querySelector('#btn-buy-credits'),
                    intro: 'Compre créditos para utilizar os recursos disponíveis.',
                    position: 'bottom'
                },
                {
                    element: document.querySelector('#stats-grid'),
                    intro: 'Acompanhe seu progresso nestes indicadores.',
                    position: 'top'
                },
                {
                    title: 'Tudo pronto!',
                    intro: 'Esperamos que tenha uma ótima experiência. Se precisar rever este tutorial, acesse "Ajuda" no menu. 🚀'
                }
            ]
        });

        function sendCompletion() {
            fetch('{{ route('onboarding.complete') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            });
        }

        intro.oncomplete(sendCompletion);
        intro.onexit(sendCompletion);

        intro.start();
    });
</script> 