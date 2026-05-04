<?php

use App\Core\Env;
use App\Core\Router;
use App\Core\Request;

/**
 * Entry Point da API (index.php).
 * 
 * Este arquivo é o "maestro" da aplicação. Todas as requisições HTTP 
 * vindas do servidor (Apache/Nginx) obrigatoriamente passam por aqui.
 */

// 1. Inicializa o Autoload do Composer para carregar todas as classes automaticamente.
require __DIR__ . '/../vendor/autoload.php';

// 2. Carrega o arquivo de configuração de rotas específicas da API.
$routes = require __DIR__ . '/../routes/api.php';

// 3. Carrega as variáveis de ambiente (DB_HOST, API_TOKEN, etc) do arquivo .env.
Env::load(__DIR__ . '/../.env');

/**
 * 4. Inicializa o Roteador passando as rotas disponíveis.
 */
$router = new Router($routes);

/**
 * 5. Captura a Requisição atual.
 * O objeto Request encapsula o método HTTP, a URI e os dados (JSON/POST).
 */
$request = new Request();

/**
 * 6. Despacha a Requisição.
 * O Router analisa o objeto $request e decide qual Controller/Método deve ser chamado.
 */
$router->dispatch($request);
