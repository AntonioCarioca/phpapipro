<?php

namespace App\Middlewares;

use App\Core\Response;
use App\Services\Logger;

/**
 * Middleware de Autorização Baseada em Papéis (RBAC).
 * * Atua como uma segunda camada de segurança após a autenticação.
 * Garante que apenas usuários com privilégios específicos (ex: admin)
 * possam acessar determinados endpoints da API.
 * * @package App\Middlewares
 * @author XxZeroxX
 * @version 1.0.1
 */
class RoleMiddleware
{
    /**
     * Valida se o usuário autenticado possui o papel de Administrador.
     * * Depende diretamente da execução prévia do JwtMiddleware, que é
     * responsável por popular a variável $_REQUEST['auth_user'].
     * * @return void
     */
    public static function admin(): void
    {
        /**
         * Recupera o objeto do usuário decodificado do token JWT.
         */
        $user = $_REQUEST['auth_user'] ?? null;

        // Se a requisição chegou aqui sem usuário autenticado, bloqueia por segurança.
        if (!$user) {
            self::forbidden();
        }

        /**
         * Verificação estrita de nível de acesso.
         * O operador ?? previne falhas fatais (Fatal Error) caso o token JWT
         * seja antigo e não possua a chave 'role' no payload.
         */
        if (($user->user->role ?? null) !== 'admin') {

            /**
             * Auditoria de Segurança:
             * Registra tentativas de escalonamento de privilégios. Adicionamos
             * o ID e o E-mail ao log para identificar exatamente qual usuário
             * tentou acessar a rota restrita.
             */
            Logger::warning(
                'Tentativa de acesso a rota restrita (Admin)',
                [
                    'user_id' => $user->user->id ?? 'Desconhecido',
                    'email'   => $user->user->email ?? 'Desconhecido',
                    'role'    => $user->user->role ?? 'Nenhuma',
                    'ip'      => $_SERVER['REMOTE_ADDR'] ?? null
                ]
            );

            self::forbidden();
        }
    }

    /**
     * Helper para padronizar a resposta de Acesso Negado.
     * * @return void
     */
    private static function forbidden(): void
    {
        /**
         * HTTP 403 Forbidden:
         * Diferente do 401 (Não Autorizado/Não Autenticado), o 403 significa:
         * "Eu reconheço seu token JWT, sei quem você é, mas você não tem
         * permissão para ver ou modificar este recurso."
         */
        Response::json([
            'success' => false,
            'message' => 'Acesso negado. Você não tem permissão para acessar este recurso.'
        ], 403);

        // Interrompe o fluxo para que o Router não chame o Controller.
        exit;
    }
}
