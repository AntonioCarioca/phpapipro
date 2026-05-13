<?php

namespace App\Controllers;

use App\Models\User;
use App\Middlewares\JwtMiddleware;
use App\Core\Response;
use App\Core\Request;

/**
 * Controlador de Recursos de Usuário (API RESTful).
 * 
 * Implementa o padrão CRUD para a entidade de usuários, utilizando
 * autenticação via Token Bearer e comunicação exclusivamente via JSON.
 * 
 * @package App\Controllers
 * @author XxZeroxX
 * @version 2.0.0
 */
class UserController
{
    /**
     * Endpoint de boas-vindas/status da API.
     * @return void
     */
    public function home(): void
    {
        Response::json(['success' => true, 'message' => 'PHP REST API Pro']);
        exit;
    }

    /**
     * Lista todos os usuários com suporte a paginação.
     * 
     * Protegido por AuthMiddleware. Aceita parâmetros 'page' e 'per_page' via URL.
     * 
     * @return void
     */
    public function index(): void
    {
        // Verifica o Token Bearer antes de processar a lógica
        JwtMiddleware::handle();

        // Tratamento de parâmetros de query string para paginação segura
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(50, (int) ($_GET['per_page'] ?? 10)));

        Response::json([
            'success' => true,
            'data'    => User::all($page, $perPage),
            'meta'    => [
                'page'     => $page, 
                'per_page' => $perPage
            ]
        ]);
    }

    /**
     * Busca um usuário pelo ID.
     * 
     * @param Request $request Objeto injetado pelo Router.
     * @param int $id Identificador vindo da URL dinâmica.
     * @return void
     */
    public function show(Request $request, int $id): void
    {
        JwtMiddleware::handle();

        $user = User::find($id);

        if (! $user) {
            Response::json(['success' => false, 'message' => 'Usuário não encontrado.'], 404);
            return;
        }

        Response::json(['success' => true, 'data' => $user]);
    }

    /**
     * Cria um novo registro de usuário.
     * 
     * Recebe dados via JSON bruto no corpo da requisição.
     * 
     * @param Request $request
     * @return void
     */
    public function store(Request $request): void
    {
        JwtMiddleware::handle();

        // Obtém os dados já decodificados (JSON ou POST) através da classe Request
        $data = $request->input();

        // Validação de integridade dos dados
        if (! $this->isValid($data)) {
            Response::json(['success' => false, 'message' => 'Dados inválidos.'], 422);
            return;
        }

        User::create($data);

        // Retorna status 201 (Created) para indicar sucesso na persistência
        Response::json([
            'success' => true,
            'message' => 'Usuário criado com sucesso.',
            'data'    => $data,
        ], 201);
    }

    /**
     * Atualiza um usuário existente.
     * 
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, int $id): void
    {
        JwtMiddleware::handle();

        // Verifica se o recurso existe antes de tentar a edição
        if (! User::find($id)) {
            Response::json(['success' => false, 'message' => 'Usuário não encontrado.'], 404);
            return;
        }

        $data = $request->input();

        if (! $this->isValid($data)) {
            Response::json(['success' => false, 'message' => 'Dados inválidos.'], 422);
            return;
        }

        User::update($id, $data);

        Response::json([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso.',
            'data'    => $data,
        ]);
    }

    /**
     * Remove um usuário do banco de dados.
     * 
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function destroy(Request $request, int $id): void
    {
        JwtMiddleware::handle();
        
        if (! User::find($id)) {
            Response::json(['success' => false, 'message' => 'Usuário não encontrado.'], 404);
            return;
        }

        User::delete($id);
        Response::json(['success' => true, 'message' => 'Usuário removido com sucesso.']);
    }

    /**
     * Helper interno para validação de campos obrigatórios.
     * 
     * @param array $data
     * @return bool
     */
    private function isValid(array $data): bool
    {
        return ! empty($data['name']) && 
               ! empty($data['email']) && 
               filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    }
}
