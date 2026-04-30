<?php

namespace App\Controllers;

use App\Models\User;
use App\Response;

class UserController
{
    public function index(): void
    {
        Response::json(['success' => true, 'data' => User::all()]);
    }

    public function show(int $id): void
    {
        $user = User::find($id);
        $user ? Response::json(['success' => true, 'data' => $user]) : Response::json(['success' => false, 'message' => 'Usuário não encontrado'], 404);
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        if (!$this->valid($data)) {
            Response::json(['success' => false, 'message' => 'Dados inválidos'], 422);
            return;
        }
        User::create($data);
        Response::json(['success' => true, 'message' => 'Usuário criado'], 201);
    }

    public function update(int $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        if (!$this->valid($data)) {
            Response::json(['success' => false, 'message' => 'Dados inválidos'], 422);
            return;
        }
        User::update($id, $data);
        Response::json(['success' => true, 'message' => 'Usuário atualizado']);
    }

    public function destroy(int $id): void
    {
        User::delete($id);
        Response::json(['success' => true, 'message' => 'Usuário removido']);
    }

    private function valid(array $data): bool
    {
        return !empty($data['name']) && !empty($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    }
}
