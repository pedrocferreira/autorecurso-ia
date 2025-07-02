<?php
// Configurações do banco de dados diretamente (sem Laravel)
$host = 'localhost';
$dbname = 'allsegte_recursos';
$username = 'allsegte_recurso_root';
$password = '@Pedro9cce22f2';

try {
    // Tenta criar uma conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Define o modo de erro para exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conexão com o banco de dados estabelecida com sucesso!<br>";
    
    // Teste para verificar se consegue consultar o banco
    $stmt = $pdo->query("SELECT VERSION() as version");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Versão do MySQL: " . $row['version'] . "<br>";
    
    // Verificar tabelas
    $stmt = $pdo->query("SHOW TABLES");
    echo "Tabelas no banco de dados:<br>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . array_values($row)[0] . "<br>";
    }
    
} catch (PDOException $e) {
    echo "Erro ao conectar com o banco de dados: " . $e->getMessage() . "<br>";
    echo "Código de erro: " . $e->getCode() . "<br>";
}

// Informações sobre PHP e PDO
echo "<br><h3>Informações do PHP:</h3>";
echo "Versão do PHP: " . phpversion() . "<br>";
echo "PDO instalado: " . (extension_loaded('pdo') ? "Sim" : "Não") . "<br>";
echo "PDO MySQL instalado: " . (extension_loaded('pdo_mysql') ? "Sim" : "Não") . "<br>";

// Listar as extensões instaladas
echo "<br><h3>Extensões PHP instaladas:</h3>";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $ext) {
    echo "- $ext<br>";
}
?> 