<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\JwtService;

/**
 * Controlador de Autenticação via API.
 * 
 * Este controlador gerencia a troca de credenciais (e-mail/senha) por 
 * um token de acesso seguro (JWT).
 * 
 * @package App\Controllers
 * @author XxZeroxX
 * @version 1.0.0
 */
class AuthController
{
    /**
     * Realiza o login do usuário e retorna um Token JWT.
     * 
     * Fluxo: 
     * 1. Valida a presença dos campos.
     * 2. Busca o usuário no banco.
     * 3. Verifica a senha criptografada.
     * 4. Gera e retorna o token em caso de sucesso.
     * 
     * @return void
     */
    public function login(): void
    {
        // Define o tipo de conteúdo como JSON
        header('Content-Type: application/json');

        // Captura o input JSON do corpo da requisição (Raw body)
        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        /**
         * Validação de Campos (422 Unprocessable Entity):
         * Verifica se as chaves necessárias existem e não estão vazias.
         */
        if (
            empty($data['email']) ||
            empty($data['password'])
        ) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Email e senha obrigatórios'
            ]);
            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($data['email']);

        /**
         * Verificação de Segurança (401 Unauthorized):
         * Usamos password_verify para comparar o texto puro com o hash do banco.
         * Importante: Usamos a mesma mensagem para usuário inexistente ou senha errada
         * para evitar "enumeração de usuários".
         */
        if (
            !$user ||
            !password_verify(
                $data['password'],
                $user['password'] // O hash deve estar na coluna 'password' do banco
            )
        ) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Credenciais inválidas'
            ]);
            return;
        }

        /**
         * Geração do Token:
         * O JwtService assina os dados do usuário, criando uma sessão stateless.
         */
        $token = JwtService::generate($user);

        // Resposta de sucesso (200 OK por padrão)
        echo json_encode([
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
