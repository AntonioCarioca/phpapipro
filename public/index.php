<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\UserController;
use App\Response;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$controller = new UserController();

if ($uri === '/api/users' && $method === 'GET') $controller->index();
elseif (preg_match('#^/api/users/(\d+)$#', $uri, $m) && $method === 'GET') $controller->show((int)$m[1]);
elseif ($uri === '/api/users' && $method === 'POST') $controller->store();
elseif (preg_match('#^/api/users/(\d+)$#', $uri, $m) && $method === 'PUT') $controller->update((int)$m[1]);
elseif (preg_match('#^/api/users/(\d+)$#', $uri, $m) && $method === 'DELETE') $controller->destroy((int)$m[1]);
else Response::json(['success' => false, 'message' => 'Rota não encontrada'], 404);
