<?php
// Arquivo de diagnóstico para problemas de redirecionamento com /public/

// Verificar a URL atual
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// Redirecionar para a URL correta sem /public/
$correct_url = str_replace('/public/', '/', $current_url);
$correct_url = str_replace('/public', '/', $correct_url);

// Função para mostrar variáveis de servidor de forma segura
function showServerVar($name) {
    return isset($_SERVER[$name]) ? htmlspecialchars($_SERVER[$name]) : 'não definido';
}

// Redirecionar automaticamente
header("Location: " . $correct_url, true, 301);
exit;
?> 