<?php
// Script para diagnóstico e correção do problema com PDO

// Função para exibir informações formatadas
function printInfo($label, $value, $isError = false) {
    $style = $isError ? 'color: red; font-weight: bold;' : '';
    echo "<div style='{$style}'><strong>{$label}:</strong> {$value}</div>";
}

// Cabeçalho da página
echo "<h1>Diagnóstico de PDO - AllSeg.tech</h1>";
echo "<p>Este script ajuda a identificar e resolver problemas com a extensão PDO no servidor.</p>";

// Verificar versão do PHP
$phpVersion = phpversion();
printInfo("Versão do PHP", $phpVersion);

// Verificar se o PDO está instalado
$pdoInstalled = extension_loaded('pdo');
printInfo("PDO instalado", $pdoInstalled ? "Sim" : "Não", !$pdoInstalled);

// Verificar se o driver PDO MySQL está instalado
$pdoMysqlInstalled = extension_loaded('pdo_mysql');
printInfo("PDO MySQL instalado", $pdoMysqlInstalled ? "Sim" : "Não", !$pdoMysqlInstalled);

// Verificar diretório de configuração do PHP
$phpIniPath = php_ini_loaded_file();
printInfo("Arquivo de configuração PHP (php.ini)", $phpIniPath ?: "Não encontrado");

// Informações do servidor
echo "<h2>Informações do Servidor</h2>";
printInfo("Servidor Web", $_SERVER['SERVER_SOFTWARE']);
printInfo("Sistema Operacional", PHP_OS);

// Verificar extensões carregadas
echo "<h2>Extensões PHP Carregadas</h2>";
$extensions = get_loaded_extensions();
sort($extensions);
echo "<div style='max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
foreach ($extensions as $ext) {
    echo "- {$ext}<br>";
}
echo "</div>";

// Verificar configurações importantes
echo "<h2>Configurações Importantes</h2>";
$configs = [
    'display_errors' => ini_get('display_errors'),
    'error_reporting' => ini_get('error_reporting'),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'post_max_size' => ini_get('post_max_size'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'extension_dir' => ini_get('extension_dir')
];

foreach ($configs as $key => $value) {
    printInfo($key, $value);
}

// Testar conexão com banco de dados
echo "<h2>Teste de Conexão com o Banco de Dados</h2>";

// Configurações do banco de dados
$host = 'localhost';
$dbname = 'allsegte_recursos';
$username = 'allsegte_recurso_root';
$password = '@Pedro9cce22f2';

try {
    if (!$pdoInstalled) {
        throw new Exception("A extensão PDO não está instalada.");
    }
    
    // Tentar conexão direta com MySQL sem PDO
    if (function_exists('mysqli_connect')) {
        $mysqli = mysqli_connect($host, $username, $password, $dbname);
        if ($mysqli) {
            printInfo("Conexão MySQLi", "Bem-sucedida");
            mysqli_close($mysqli);
        } else {
            printInfo("Conexão MySQLi", "Falhou: " . mysqli_connect_error(), true);
        }
    } else {
        printInfo("MySQLi", "Não disponível", true);
    }
    
    // Tentar conexão com PDO
    if ($pdoInstalled && $pdoMysqlInstalled) {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        printInfo("Conexão PDO", "Bem-sucedida");
        
        // Testar uma consulta
        $stmt = $pdo->query("SELECT VERSION() as version");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        printInfo("Versão do MySQL", $row['version']);
        
        // Listar tabelas
        $stmt = $pdo->query("SHOW TABLES");
        $tables = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tables[] = array_values($row)[0];
        }
        printInfo("Número de tabelas", count($tables));
        
        if (count($tables) > 0) {
            echo "<details>";
            echo "<summary>Mostrar lista de tabelas</summary>";
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>{$table}</li>";
            }
            echo "</ul>";
            echo "</details>";
        }
    }
} catch (Exception $e) {
    printInfo("Erro de conexão", $e->getMessage(), true);
    printInfo("Código de erro", $e->getCode(), true);
}

// Instruções para corrigir o problema
echo "<h2>Passos para Corrigir o Problema com PDO</h2>";
echo "<ol>";
echo "<li>Entre em contato com o suporte de hospedagem e solicite a ativação da extensão PDO para PHP " . $phpVersion . "</li>";
echo "<li>Peça para verificar se o PHP utilizado pelo servidor web (Apache/FPM) está com PDO habilitado</li>";
echo "<li>Em cPanel, acesse a seção PHP Selector ou MultiPHP Manager e certifique-se de que a extensão PDO está ativada</li>";
echo "<li>Certifique-se de que seu site está usando a versão correta do PHP (" . $phpVersion . ")</li>";
echo "</ol>";

// Sugestões de correção temporária
echo "<h2>Correções Temporárias</h2>";
echo "<p>Enquanto o problema não é resolvido pelo suporte de hospedagem, você pode:</p>";
echo "<ol>";
echo "<li>Adicionar <code>AddHandler application/x-httpd-ea-php82 .php</code> ao arquivo .htaccess (já feito)</li>";
echo "<li>Criar um arquivo .user.ini na pasta public com as configurações das extensões (já feito)</li>";
echo "<li>Reiniciar o servidor web (peça ao suporte para fazer isso)</li>";
echo "</ol>";

// Verificar configurações do Apache
echo "<h2>Verificar Configuração do Apache</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "<p>Módulos Apache carregados:</p>";
    echo "<div style='max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    sort($modules);
    foreach ($modules as $mod) {
        echo "- {$mod}<br>";
    }
    echo "</div>";
} else {
    echo "<p>Não foi possível obter os módulos do Apache (a função apache_get_modules não está disponível).</p>";
}

// Informações adicionais
echo "<h2>Registro de Novos Usuários</h2>";
echo "<p>O problema atual está impedindo o registro de novos usuários porque o Laravel não consegue se conectar ao banco de dados para validar se o e-mail já está em uso.</p>";
echo "<p>Este erro ocorre porque a extensão PDO não está disponível para o PHP que está sendo usado pelo servidor web, embora esteja disponível na linha de comando.</p>";

// Resumo dos passos
echo "<h2>Resumo das Etapas para Solução</h2>";
echo "<ol>";
echo "<li>Habilitamos a exibição de erros detalhados</li>";
echo "<li>Adicionamos instruções para usar PHP 8.2 no arquivo .htaccess</li>";
echo "<li>Criamos um arquivo .user.ini para tentar carregar as extensões necessárias</li>";
echo "<li>Corrigimos o valor LOG_CHANNEL no arquivo .env</li>";
echo "<li>O próximo passo é contatar o suporte de hospedagem para habilitar o PDO na configuração do PHP do servidor web</li>";
echo "</ol>";

echo "<p><strong>Importante:</strong> Após as alterações feitas, tente acessar a <a href='/register'>página de registro</a> novamente para ver se o problema foi resolvido.</p>";
?> 