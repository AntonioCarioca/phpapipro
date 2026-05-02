<?php

namespace App\Middleware;

use App\Response;

/**
 * Middleware de Autenticação via Token (API).
 * 
 * Esta classe implementa uma camada de segurança para rotas de API,
 * validando a presença e a integridade de um token Bearer enviado
 * no cabeçalho Authorization da requisição HTTP.
 * 
 * @package App\Middleware
 * @author XxZeroxX
 * @version 1.0.0
 */
class AuthMiddleware
{
    /**
     * Valida o token de autorização da requisição.
     * 
     * Compara o token recebido no cabeçalho 'HTTP_AUTHORIZATION' com o token
     * mestre definido nas variáveis de ambiente ($_ENV['API_TOKEN']).
     * Caso os tokens não coincidam, retorna uma resposta JSON com status 401 (Unauthorized).
     * 
     * @return void
     */
    public static function check(): void
    {
        // Recupera o cabeçalho Authorization enviado pelo cliente
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        /**
         * Define o token esperado.
         * Formato padrão: 'Bearer {token}'
         */
        $expected = 'Bearer ' . ($_ENV['API_TOKEN'] ?? 'seu-token-secreto');

        // Verificação de segurança (Comparação estrita)
        if ($header !== $expected) {
            /**
             * Utiliza a classe Helper de resposta para retornar um JSON padronizado.
             * 401 Unauthorized: Indica que a requisição carece de credenciais válidas.
             */
            Response::json([
                'success' => false, 
                'message' => 'Token inválido ou ausente.'
            ], 401);
            
            // Interrompe o ciclo de vida da aplicação para garantir que o Controller não seja executado
            exit;
        }
    }
}
