<?php

namespace App\Controllers;

use App\Models\User;
use App\Response;

/**
 * Controlador de Recursos de Usuário (API).
 * 
 * Responsável por expor os endpoints de manipulação de usuários,
 * garantindo a validação de dados e retornando respostas em formato JSON.
 * 
 * @package App\Controllers
 * @author XxZeroxX
 * @version 1.0.0
 */
class UserController
{
    /**
     * Lista usuários com suporte a paginação.
     * 
     * Captura parâmetros 'page' e 'per_page' via Query String.
     * 
     * @return void
     */
    public function index(): void
    {
        // Garante que a página seja no mínimo 1
        $page = max(1, (int) ($_GET['page'] ?? 1));
        // Limita a quantidade de itens por página entre 1 e 50 por segurança
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
     * Exibe os detalhes de um usuário específico.
     * 
     * @param int $id Identificador do usuário.
     * @return void
     */
    public function show(int $id): void
    {
        $user = User::find($id);

        if (! $user) {
            Response::json(['success' => false, 'message' => 'Usuário não encontrado.'], 404);
            return;
        }

        Response::json(['success' => true, 'data' => $user]);
    }

    /**
     * Cria um novo usuário.
     * 
     * Lê o corpo da requisição (JSON) e valida os campos obrigatórios.
     * 
     * @return void
     */
    public function store(): void
    {
        // php://input permite ler dados brutos enviados via POST/PUT (JSON)
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if (! $this->isValid($data)) {
            Response::json(['success' => false, 'message' => 'Dados inválidos.'], 422);
            return;
        }

        User::create($data);
        Response::json(['success' => true, 'message' => 'Usuário criado com sucesso.'], 201);
    }

    /**
     * Atualiza os dados de um usuário existente.
     * 
     * @param int $id Identificador do usuário.
     * @return void
     */
    public function update(int $id): void
    {
        // Verifica existência antes de tentar atualizar
        if (! User::find($id)) {
            Response::json(['success' => false, 'message' => 'Usuário não encontrado.'], 404);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if (! $this->isValid($data)) {
            Response::json(['success' => false, 'message' => 'Dados inválidos.'], 422);
            return;
        }

        User::update($id, $data);
        Response::json(['success' => true, 'message' => 'Usuário atualizado com sucesso.']);
    }

    /**
     * Remove um usuário do sistema.
     * 
     * @param int $id Identificador do usuário.
     * @return void
     */
    public function destroy(int $id): void
    {
        if (! User::find($id)) {
            Response::json(['success' => false, 'message' => 'Usuário não encontrado.'], 404);
            return;
        }

        User::delete($id);
        Response::json(['success' => true, 'message' => 'Usuário removido com sucesso.']);
    }

    /**
     * Valida os dados obrigatórios para criação e atualização.
     * 
     * @param array $data Dados a serem validados.
     * @return bool Retorna true se os dados forem válidos.
     */
    private function isValid(array $data): bool
    {
        return ! empty($data['name']) && 
               ! empty($data['email']) && 
               filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    }
}
