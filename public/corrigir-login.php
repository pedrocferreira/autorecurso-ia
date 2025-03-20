<?php
// Remover qualquer cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Verificar se existe o cookie de login-estatico
$has_cookie = isset($_COOKIE['login_estatico_redirect']);

// Definir um cookie para evitar redirecionamentos futuros
setcookie('prevent_login_estatico', '1', time() + 60*60*24*30, '/', '', true, true);

// Se o usuário clicou para corrigir
if (isset($_GET['fix'])) {
    // Limpar cache de redirecionamento
    setcookie('login_estatico_redirect', '', time() - 3600, '/', '', true, true);
    
    // Armazenar a informação de correção
    setcookie('login_fixed', '1', time() + 60*60*24*30, '/', '', true, true);
    
    // Redirecionar para a página de login normal
    header('Location: /login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corrigir Problema de Login - AutoRecurso</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 800px; margin: 0 auto; }
        h1 { color: #3b82f6; }
        .section { border: 1px solid #ddd; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
        .button { display: inline-block; background-color: #3b82f6; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; margin: 10px 5px 10px 0; cursor: pointer; border: none; font-size: 16px; }
        .button:hover { background-color: #2563eb; }
        .warning { background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Corrigir Problema de Login - AutoRecurso</h1>
    
    <div class="section">
        <h2>Problema de Redirecionamento Detectado</h2>
        <p>Você está enfrentando um problema onde ao clicar em "Entrar", está sendo redirecionado para /login-estatico.html em vez da página de login normal.</p>
        
        <div class="warning">
            <p><strong>Atenção:</strong> Esse problema pode ser causado por:</p>
            <ul>
                <li>Cache do navegador</li>
                <li>Configurações do servidor</li>
                <li>Redirecionamentos salvos no navegador</li>
            </ul>
        </div>
    </div>
    
    <div class="section">
        <h2>Ações Corretivas</h2>
        <p>Clique no botão abaixo para tentar corrigir o problema de redirecionamento:</p>
        
        <a href="?fix=1" class="button">Corrigir Problema de Login</a>
        
        <p>Após clicar no botão acima, você será redirecionado para a página de login normal e o problema deve ser resolvido.</p>
    </div>
    
    <div class="section">
        <h2>Passos Adicionais</h2>
        <ol>
            <li>Limpe o cache do seu navegador (Ctrl+F5 ou Cmd+Shift+R)</li>
            <li>Exclua cookies do site allseg.tech</li>
            <li>Tente usar um navegador diferente ou uma janela anônima/privativa</li>
        </ol>
    </div>
    
    <div class="section">
        <h2>Links Diretos</h2>
        <p>Use estes links diretos para acessar as páginas:</p>
        
        <a href="/login" class="button">Página de Login</a>
        <a href="/register" class="button">Página de Registro</a>
        <a href="/" class="button">Página Inicial</a>
    </div>
    
    <script>
        // Limpar qualquer estado de navegação que possa causar redirecionamentos
        if (window.history && window.history.replaceState) {
            window.history.replaceState({}, document.title, window.location.pathname + window.location.search);
        }
        
        // Limpar cache de redirecionamento
        document.addEventListener('DOMContentLoaded', function() {
            // Definir localStorage para evitar redirecionamentos
            localStorage.setItem('prevent_login_estatico', '1');
            sessionStorage.setItem('prevent_login_estatico', '1');
            
            // Remover quaisquer entradas de histórico que possam ter login-estatico
            if (window.location.href.indexOf('login-estatico') === -1) {
                localStorage.removeItem('last_login_url');
            }
        });
    </script>
</body>
</html> 