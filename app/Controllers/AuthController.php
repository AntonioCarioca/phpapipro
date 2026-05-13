<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\JwtService;
use App\Core\Response;
use App\Core\Request;

/**
 * Controlador de Autenticação via API.
 * 
 * Gerencia o ciclo de vida da autenticação stateless, transformando 
 * credenciais válidas em tokens JWT assinados.
 * 
 * @package App\Controllers
 * @author XxZeroxX
 * @version 1.0.1
 */
class AuthController
{
    /**
     * Endpoint de Autenticação (Login).
     * 
     * Recebe e-mail e senha, valida as credenciais contra o hash no banco 
     * de dados e retorna um token de acesso para uso em rotas protegidas.
     * 
     * @param Request $request Objeto de requisição injetado automaticamente pelo Router.
     * @return void
     */
    public function login(Request $request): void
    {
        /**
         * 1. Coleta de Dados:
         * O método input() abstrai se os dados vieram via JSON ou POST clássico.
         */
        $data = $request->input();

        /**
         * 2. Validação de Presença (HTTP 422):
         * Garante que a requisição contém o mínimo necessário para processar.
         */
        if (empty($data['email']) || empty($data['password'])) {
            Response::json([
                'success' => false, 
                'message' => 'Email e senha obrigatórios'
            ], 422);
            return;
        }

        /**
         * 3. Busca e Verificação:
         * Instancia o Model e busca o usuário pelo e-mail fornecido.
         */
        $userModel = new User();
        $user = $userModel->findByEmail($data['email']);

        /**
         * 4. Validação de Segurança (HTTP 401):
         * Se o usuário não existir ou o password_verify falhar, retornamos 
         * um erro genérico para dificultar ataques de força bruta.
         */
        if (!$user || !password_verify($data['password'], $user['password'])) {
            Response::json([
                'success' => false, 
                'message' => 'Credenciais inválidas'
            ], 401);
            return;
        }

        /**
         * 5. Emissão do Token (JWT):
         * Com as credenciais confirmadas, geramos a assinatura digital que 
         * identifica o usuário nas próximas requisições (Stateless).
         */
        $token = JwtService::generate($user);

        /**
         * 6. Resposta Final (HTTP 200):
         * Retornamos o token e dados básicos (não sensíveis) do usuário.
         */
        Response::json([
            'success' => true,
            'token'   => $token,
            'user'    => [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email']
            ]
        ]);
    }
}
