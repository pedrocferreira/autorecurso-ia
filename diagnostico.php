<?php
// Desabilitar o cache do navegador
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Função para mostrar variáveis de servidor de forma segura
function showServerVar($name) {
    return isset($_SERVER[$name]) ? htmlspecialchars($_SERVER[$name]) : 'não definido';
}

// URL atual
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico de Redirecionamento</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; }
        h1 { color: #333; }
        .section { margin-bottom: 20px; padding: 15px; background: #f5f5f5; border-radius: 5px; }
        .var { margin: 10px 0; }
        .key { font-weight: bold; }
        .value { font-family: monospace; word-break: break-all; }
    </style>
</head>
<body>
    <h1>Diagnóstico de Redirecionamento Laravel</h1>
    
    <div class="section">
        <h2>URL Atual</h2>
        <div class="var">
            <span class="value"><?php echo htmlspecialchars($current_url); ?></span>
        </div>
    </div>
    
    <div class="section">
        <h2>Variáveis do Servidor</h2>
        <div class="var">
            <span class="key">REQUEST_URI:</span> 
            <span class="value"><?php echo showServerVar('REQUEST_URI'); ?></span>
        </div>
        <div class="var">
            <span class="key">SCRIPT_NAME:</span> 
            <span class="value"><?php echo showServerVar('SCRIPT_NAME'); ?></span>
        </div>
        <div class="var">
            <span class="key">SCRIPT_FILENAME:</span> 
            <span class="value"><?php echo showServerVar('SCRIPT_FILENAME'); ?></span>
        </div>
        <div class="var">
            <span class="key">DOCUMENT_ROOT:</span> 
            <span class="value"><?php echo showServerVar('DOCUMENT_ROOT'); ?></span>
        </div>
        <div class="var">
            <span class="key">PHP_SELF:</span> 
            <span class="value"><?php echo showServerVar('PHP_SELF'); ?></span>
        </div>
        <div class="var">
            <span class="key">HTTP_HOST:</span> 
            <span class="value"><?php echo showServerVar('HTTP_HOST'); ?></span>
        </div>
    </div>
    
    <div class="section">
        <h2>Informações do Servidor</h2>
        <div class="var">
            <span class="key">Caminho do index.php:</span> 
            <span class="value"><?php echo __FILE__; ?></span>
        </div>
        <div class="var">
            <span class="key">Diretório atual:</span> 
            <span class="value"><?php echo getcwd(); ?></span>
        </div>
        <div class="var">
            <span class="key">Versão do PHP:</span> 
            <span class="value"><?php echo phpversion(); ?></span>
        </div>
    </div>
    
    <div class="section">
        <h2>Links de Teste</h2>
        <div class="var">
            <a href="/">Link para raiz (/)</a>
        </div>
        <div class="var">
            <a href="/public">Link para /public</a>
        </div>
    </div>
</body>
</html> 