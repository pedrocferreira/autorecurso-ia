<?php
// Remover qualquer cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico de Login - AutoRecurso</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 800px; margin: 0 auto; }
        h1 { color: #3b82f6; }
        .section { border: 1px solid #ddd; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
        .log { background-color: #f5f5f5; padding: 10px; border-radius: 3px; font-family: monospace; white-space: pre-wrap; }
        .button { display: inline-block; background-color: #3b82f6; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; margin: 10px 5px 10px 0; }
        .button:hover { background-color: #2563eb; }
    </style>
</head>
<body>
    <h1>Diagnóstico de Login - AutoRecurso</h1>
    
    <div class="section">
        <h2>Informações da Sessão</h2>
        <div class="log">
            URL atual: <?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
            
            Referrer: <?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Não disponível'; ?>
            
            User Agent: <?php echo $_SERVER['HTTP_USER_AGENT']; ?>
        </div>
    </div>
    
    <div class="section">
        <h2>Links de Teste</h2>
        <p>Clique nos links abaixo para testar as rotas de login:</p>
        
        <a href="/login" class="button">Página de Login Normal</a>
        <a href="/login-estatico.html" class="button">Login Estático (redirecionador)</a>
    </div>
    
    <div class="section">
        <h2>Instruções para Resolver Problemas de Redirecionamento</h2>
        <ol>
            <li>Limpe o cache do seu navegador (Ctrl+F5 ou Cmd+Shift+R)</li>
            <li>Tente usar um navegador diferente</li>
            <li>Desative extensões do navegador temporariamente</li>
            <li>Tente acessar em uma janela anônima/privativa</li>
        </ol>
    </div>
    
    <div class="section">
        <h2>Resultado do Diagnóstico</h2>
        <p>Se você está vendo esta página, isso significa que:</p>
        <ol>
            <li>O servidor está funcionando corretamente</li>
            <li>A rota /login-diagnostico.php está acessível</li>
            <li>O problema pode estar relacionado a redirecionamentos específicos do navegador ou configurações do servidor</li>
        </ol>
    </div>
    
    <script>
        // Registrar eventos de clique em links
        document.addEventListener('DOMContentLoaded', function() {
            var links = document.querySelectorAll('a');
            links.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    console.log('Clicou no link: ' + this.href);
                });
            });
            
            // Registrar URL atual
            console.log('URL atual: ' + window.location.href);
            console.log('Referrer: ' + document.referrer);
        });
    </script>
</body>
</html> 