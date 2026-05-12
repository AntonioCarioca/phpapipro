<?php

use App\Controllers\UserController;
use App\Controllers\AuthController;

return [
	['GET', '/', [UserController::class, 'home']],
	['GET', '/api/users', [UserController::class, 'index']],
	['GET', '/api/users/{id}', [UserController::class, 'show']],
	['POST', '/api/users', [UserController::class, 'store']],
	['PUT', '/api/users/{id}', [UserController::class, 'update']],
	['DELETE', '/api/users/{id}', [UserController::class, 'destroy']],

	['POST', 'api/login', [AuthController::class, 'login']],
];
