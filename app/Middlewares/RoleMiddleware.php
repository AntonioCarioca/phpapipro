<?php

namespace App\Middlewares;

use App\Core\Response;

class RoleMiddleware
{
    /**
     * Restringe o acesso exclusivamente a usuários com a função 'admin'.
     *
     * Verifica se o objeto do usuário foi injetado na requisição global
     * e se a propriedade 'role' corresponde exatamente a 'admin'.
     *
     * @return void
     */
    public static function admin(): void
    {
        /**
         * Recupera o usuário autenticado que foi previamente salvo
         * na superglobal $_REQUEST pelo middleware de JWT.
         */
        $user = $_REQUEST['auth_user'] ?? null;

        // Se não houver usuário autenticado na requisição, barra imediatamente
        if (!$user) {
            self::forbidden();
        }

        /**
         * Verifica o nível de acesso.
         * O operador de coalescência nula (??) evita erros de "Property of non-object"
         * caso a estrutura do payload mude.
         */
        if (($user->user->role ?? null) !== 'admin') {
            self::forbidden();
        }
    }

    /**
     * Interrompe a requisição e retorna um erro 403 HTTP.
     *
     * @return void
     */
    private static function forbidden(): void
    {
        // 403 Forbidden: O servidor entendeu a requisição, mas o cliente não tem direito ao recurso.
        Response::json([
            'success' => false,
            'message' => 'Acesso negado. Você não tem permissão para acessar este recurso.'
        ], 403);

        // Encerra o script para impedir que o Controller processe a ação
        exit;
    }
}
