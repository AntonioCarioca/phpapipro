<?php

namespace App\Middlewares;

use App\Services\JwtService;
use App\Core\Response;
use Exception;

/**
 * Middleware de Autenticação JWT.
 * 
 * Responsável por interceptar a requisição, extrair o Token Bearer dos cabeçalhos
 * e validar a autenticidade do usuário antes que o Controller seja executado.
 * 
 * @package App\Middlewares
 */
class JwtMiddleware
{
    /**
     * Processa a validação do token.
     * 
     * @return void
     */
    public static function handle(): void
    {
        // Obtém todos os cabeçalhos da requisição HTTP
        $headers = getallheaders();

        // Captura o cabeçalho de Autorização (case-sensitive dependendo do servidor)
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        /**
         * Regex para extrair o token do formato "Bearer {token}".
         * \s representa um espaço e (\S+) captura qualquer caractere que não seja espaço.
         */
        if (!preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
            self::unauthorized();
        }

        // O índice [1] contém apenas a string do token, sem o prefixo "Bearer"
        $token = $matches[1];

        try {
            /**
             * Chama o serviço de validação.
             * Se o token estiver expirado ou a assinatura for inválida, 
             * uma Exception será lançada.
             */
            $decoded = JwtService::validate($token);

            $_REQUEST['auth_user'] = $decoded;
        } catch (Exception $e) {
            // Log do erro pode ser adicionado aqui para debug: error_log($e->getMessage());
            self::unauthorized();
        }
    }

    /**
     * Interrompe a requisição e retorna erro 401.
     * 
     * @return void
     */
    private static function unauthorized(): void
    {
        // Define o status HTTP como 401 Unauthorized
        Response::json([
            'success' => false,
            'message' => 'Token inválido, ausente ou expirado.'
        ], 401);
        // Encerra a execução para que o Controller não seja alcançado
        exit;
    }
}
