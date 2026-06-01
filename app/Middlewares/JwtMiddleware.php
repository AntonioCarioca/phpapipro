<?php

namespace App\Middlewares;

use Exception;
use App\Core\Response;
use App\Services\JwtService;
use App\Services\Logger;

/**
 * Middleware de Autenticação JWT.
 * * Atua como o "porteiro" da aplicação. Intercepta a requisição, extrai o Token
 * Bearer dos cabeçalhos e valida a assinatura digital. Em caso de fraude, expiração
 * ou ausência do token, a requisição é bloqueada e o incidente é logado.
 * * @package App\Middlewares
 * @author XxZeroxX
 * @version 1.0.3
 */
class JwtMiddleware
{
    /**
     * Processa o fluxo principal de validação do token.
     * * @return void
     */
    public static function handle(): void
    {
        // Obtém todos os cabeçalhos da requisição HTTP (suporta Apache/Nginx)
        $headers = getallheaders();

        // Captura o cabeçalho de Autorização lidando com possíveis variações de case (maiúsculo/minúsculo)
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        /**
         * Regex para extrair o token do formato "Bearer {token}".
         * O padrão \s representa um espaço, garantindo que a formatação esteja correta.
         */
        if (!preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
            self::unauthorized();
        }

        // O índice [1] contém a chave JWT limpa, pronta para decodificação
        $token = $matches[1];

        try {
            /**
             * Aciona o serviço de validação (que checa expiração, emissor e assinatura).
             * Se a validação passar, o payload decodificado é devolvido.
             */
            $decoded = JwtService::validate($token);

            /**
             * Injeção de Dependência na Requisição:
             * Salva os dados do usuário autenticado para que os próximos Middlewares
             * (como o RoleMiddleware) e os Controllers possam utilizá-los.
             */
            $_REQUEST['auth_user'] = $decoded;
        } catch (Exception $e) {
            /**
             * Auditoria de Segurança:
             * Em vez de expor detalhes do erro (ex: "Token expirado há 5 minutos") para
             * o usuário final, registramos o incidente internamente com o IP de origem.
             */
            Logger::warning(
                'Falha na validação do JWT',
                [
                    'exception' => $e->getMessage(),
                    'ip'        => $_SERVER['REMOTE_ADDR'] ?? 'Desconhecido'
                ]
            );

            // Retorna a resposta genérica padrão de negação
            self::unauthorized();
        }
    }

    /**
     * Helper interno para interromper a requisição e retornar o erro HTTP 401.
     * * @return void
     */
    private static function unauthorized(): void
    {
        // Aciona o Core Response para garantir o formato JSON padrão da API
        Response::json([
            'success' => false,
            'message' => 'Token inválido, ausente ou expirado.'
        ], 401);

        // exit; garante que o Router pare de processar e o Controller nunca seja atingido
        exit;
    }
}
