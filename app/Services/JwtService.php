<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Serviço de Gerenciamento de JWT (JSON Web Token).
 *
 * Esta classe abstrai a biblioteca Firebase JWT para gerar e validar tokens
 * de acesso, permitindo uma autenticação stateless (sem estado) segura.
 *
 * @package App\Services
 * @author XxZeroxX
 * @version 1.0.0
 */
class JwtService
{
    /**
     * Gera um novo token JWT para um usuário.
     *
     * O token contém declarações (claims) padrão como emissor, data de criação
     * e expiração, além dos dados básicos do usuário.
     *
     * @param array $user Dados do usuário (id, email).
     * @return string Token JWT codificado e assinado.
     */
    public static function generate(array $user): string
    {
        /**
         * Payload (Corpo do Token):
         * iss: Issuer (Quem emitiu o token).
         * iat: Issued At (Timestamp de quando foi criado).
         * exp: Expiration (Timestamp de quando o token expira).
         * user: Dados customizados para identificar o dono do token.
         */
        $payload = [
            'iss'  => 'php-rest-api-pro',
            'iat'  => time(),
            'exp'  => time() + ($_ENV['JWT_EXPIRE'] ?? 3600),
            'user' => [
                'id'    => $user['id'],
                'email' => $user['email'],
                'role'  => $user['role']
            ]
        ];

        /**
         * Assinatura do Token:
         * Utiliza o segredo do .env e o algoritmo HS256 para garantir que
         * o conteúdo não seja alterado por terceiros.
         */
        return JWT::encode(
            $payload,
            $_ENV['JWT_SECRET'],
            'HS256'
        );
    }

    /**
     * Valida e decodifica um token JWT.
     *
     * Caso o token seja inválido, expirado ou a assinatura não coincida,
     * a biblioteca disparará uma Exception que deve ser capturada pelo chamador.
     *
     * @param string $token O token enviado pelo cliente (geralmente via Bearer).
     * @return object Objeto contendo os dados decodificados do payload.
     * @throws \Exception Caso a validação falhe.
     */
    public static function validate(string $token): object
    {
        return JWT::decode(
            $token,
            new Key($_ENV['JWT_SECRET'], 'HS256')
        );
    }
}
