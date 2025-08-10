<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * Este arquivo carrega a aplicação Laravel a partir da raiz
 * em vez do diretório public/ padrão.
 */

// Define o diretório atual como diretório base
$publicPath = __DIR__ . '/public';
$appPath = __DIR__;

// Carrega o autoloader do Composer 
require $appPath . '/vendor/autoload.php';

// Carrega o framework Laravel
$app = require_once $appPath . '/bootstrap/app.php';

// Definir o caminho público corretamente
$app->bind('path.public', function() use ($publicPath) {
    return $publicPath;
});

// Executar a aplicação
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response); 