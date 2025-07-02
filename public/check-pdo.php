<?php
// Verifica se a extensão PDO está disponível
if (extension_loaded('pdo')) {
    echo "Extensão PDO está habilitada.<br>";
    
    // Verifica drivers PDO disponíveis
    echo "Drivers PDO disponíveis: " . implode(", ", PDO::getAvailableDrivers()) . "<br>";
} else {
    echo "Extensão PDO NÃO está habilitada! Este é o problema.<br>";
}

// Verifica outras extensões importantes para o Laravel
$required_extensions = [
    'pdo_mysql',  // Para MySQL
    'openssl',    // Para segurança
    'mbstring',   // Para manipulação de strings
    'tokenizer',  // Para processamento de tokens PHP
    'xml',        // Para processamento XML
    'ctype',      // Para verificação de tipos de caracteres
    'json',       // Para processamento JSON
    'bcmath',     // Para cálculos de precisão arbitrária
    'fileinfo'    // Para informações de arquivo
];

echo "<br>Verificando outras extensões necessárias:<br>";
foreach ($required_extensions as $ext) {
    echo $ext . ": " . (extension_loaded($ext) ? "Habilitada" : "NÃO habilitada") . "<br>";
}

// Verificar versão do PHP
echo "<br>Versão do PHP: " . phpversion() . "<br>";

// Verificar configurações do php.ini
echo "<br>Configurações importantes:<br>";
echo "display_errors: " . ini_get('display_errors') . "<br>";
echo "error_reporting: " . ini_get('error_reporting') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
?> 